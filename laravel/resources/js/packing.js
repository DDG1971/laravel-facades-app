// resources/js/packing.js

let splitMode = false;

document.addEventListener('DOMContentLoaded', () => {
    // Кнопка разделения
    const splitBtn = document.getElementById('splitItemsBtn');
    if (splitBtn) {
        splitBtn.onclick = () => {
            splitMode = !splitMode;
            splitBtn.textContent = splitMode ? 'Сгруппировать' : 'Разделить на единицы';
            splitBtn.classList.toggle('bg-blue-600');
            splitBtn.classList.toggle('text-white');
            renderItemsList();
        };
    }

    // Кнопки создания коробок
    document.getElementById('createFirstBox')?.addEventListener('click', createBox);
    document.getElementById('addBoxBtn')?.addEventListener('click', createBox);

    // Печать всех
    const printAllBtn = document.getElementById('printAllBoxes');
    if (printAllBtn) {
        printAllBtn.addEventListener('click', () => {
            document.querySelectorAll('.box-dropzone').forEach((box, i) => {
                setTimeout(() => window.open(`/boxes/${box.dataset.boxId}/print`, '_blank'), i * 300);
            });
        });
    }

    renderItemsList();
    loadBoxesContent();
});

// Отрисовка списка деталей
function renderItemsList() {
    const container = document.getElementById('items-list');
    if (!container) return;

    const items = JSON.parse(container.dataset.items || '[]');
    let html = '';

    items.forEach(item => {
        const typeName = item.type ? item.type + ' ' : '';
        if (splitMode) {
            for (let i = 0; i < item.quantity; i++) {
                html += createItemCard({...item, type: typeName.trim()}, 1, i);
            }
        } else {
            html += createItemCard({...item, type: typeName.trim()}, item.quantity);
        }
    });

    container.innerHTML = html || '<p class="text-gray-400">Все детали упакованы</p>';
    initDragEvents();
}

// Карточка детали
function createItemCard(item, qty, unitIndex = null) {
    const unitId = unitIndex !== null ? `${item.id}_${unitIndex}` : item.id;
    const typeName = item.type ? item.type + ' ' : '';
    return `
        <div class="item-card border rounded p-2 cursor-move bg-gray-50 hover:bg-gray-100"
             draggable="true"
             data-item-id="${item.id}"
             data-unit-id="${unitId}"
             data-qty="${qty}"
             data-height="${item.height}"
             data-width="${item.width}"
             data-thickness="${item.thickness || 19}">
            <strong>${typeName}${item.height}x${item.width}</strong> × ${qty} шт
            <br><small class="text-gray-500">${item.thickness || 19} мм</small>
        </div>
    `;
}

// Drag-and-Drop
function initDragEvents() {
    document.querySelectorAll('.item-card').forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    document.querySelectorAll('.box-dropzone').forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
    });
}

let draggedItem = null;

function handleDragStart(e) {
    draggedItem = this;
    this.classList.add('opacity-50');
    e.dataTransfer.setData('text/plain', this.dataset.unitId);
}

function handleDragEnd(e) {
    this.classList.remove('opacity-50');
    draggedItem = null;
}

function handleDragOver(e) {
    e.preventDefault();
    this.classList.add('border-blue-400');
}
// Обновление itemsData после перетаскивания
function updateItemsData(itemId, qtyRemoved) {
    const container = document.getElementById('items-list');
    let items = JSON.parse(container.dataset.items || '[]');

    // Приводим itemId к числу для надёжности
    const id = parseInt(itemId);

    const itemIndex = items.findIndex(i => parseInt(i.id) === id);

    if (itemIndex !== -1) {
        items[itemIndex].quantity -= qtyRemoved;
        if (items[itemIndex].quantity <= 0) {
            items.splice(itemIndex, 1);
        }
    }

    container.dataset.items = JSON.stringify(items);
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400');

    const boxId = this.dataset.boxId;
    const unitId = e.dataTransfer.getData('text/plain');
    const itemCard = document.querySelector(`[data-unit-id="${unitId}"]`);

    if (!itemCard || !boxId) return;

    const itemId = itemCard.dataset.itemId;
    const qty = parseInt(itemCard.dataset.qty) || 1;

    addItemToBox(boxId, itemId, qty).then(() => {
        // Обновляем данные ДО удаления карточки
        updateItemsData(itemId, qty);

        itemCard.remove();
        loadBoxContent(boxId);

        if (document.querySelectorAll('.item-card').length === 0) {
            document.getElementById('items-list').innerHTML = '<p class="text-gray-400">Все детали упакованы</p>';
        }
    });
}
// API: Добавить деталь
async function addItemToBox(boxId, itemId, quantity) {
    const orderId = document.querySelector('meta[name="order-id"]')?.content;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch(`/orders/${orderId}/boxes/${boxId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_item_id: itemId, quantity })
    });

    return response.json();
}

// API: Создать коробку
async function createBox() {
    console.log('createBox вызвана!');
    const orderId = document.querySelector('meta[name="order-id"]')?.content;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch(`/orders/${orderId}/boxes`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    });

    const data = await response.json();
    if (data.box) {
        addBoxToUI(data.box);

        // Скрываем кнопку "Создать первую коробку"
        document.getElementById('createFirstBox')?.closest('.col-span-2')?.classList.add('hidden');

        // Показываем кнопки
        document.getElementById('addBoxBtn')?.classList.remove('hidden');
        document.getElementById('printAllBoxes')?.classList.remove('hidden');

        // Обновляем счётчик
        const countSpan = document.getElementById('boxesCount');
        if (countSpan) {
            countSpan.textContent = document.querySelectorAll('.box-dropzone').length;
        }
    }
}

// Добавление коробки в UI
function addBoxToUI(box) {
    const container = document.getElementById('boxes-container');
    const boxHtml = `
        <div class="bg-white shadow rounded p-4 box-dropzone" data-box-id="${box.id}">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold">Коробка #${box.box_number}</h3>
                <a href="/boxes/${box.id}/print" class="text-blue-600 hover:underline text-sm" target="_blank">
                    🖨️ Печать
                </a>
            </div>
            <div class="box-items min-h-[150px] border border-dashed border-gray-300 rounded p-2 bg-gray-50">
                <p class="text-gray-400 text-sm">Перетащите детали сюда</p>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', boxHtml);
    initDragEvents();

    // Обновляем счётчик коробок
    const countSpan = document.getElementById('boxesCount');
    if (countSpan) {
        countSpan.textContent = document.querySelectorAll('.box-dropzone').length;
    }
}


// Загрузка содержимого всех коробок
function loadBoxesContent() {
    document.querySelectorAll('.box-dropzone').forEach(zone => {
        loadBoxContent(zone.dataset.boxId);
    });
}

// Загрузка содержимого одной коробки
async function loadBoxContent(boxId) {
    const container = document.querySelector(`.box-dropzone[data-box-id="${boxId}"] .box-items`);
    if (!container) return;

    const orderId = document.querySelector('meta[name="order-id"]')?.content;

    const response = await fetch(`/orders/${orderId}/boxes/${boxId}/items`);
    const data = await response.json();

    let html = '';
    data.items.forEach(item => {
        html += `
            <div class="text-sm py-1 border-b flex justify-between items-center">
                <span>
                    ${item.type ? item.type + ' ' : ''}${item.height}x${item.width} × ${item.quantity} шт
                    <span class="text-gray-400">(${item.thickness || 19} мм)</span>
                </span>
                <button class="return-item text-red-500 hover:text-red-700 text-xs px-2"
                        data-box-id="${boxId}"
                        data-item-id="${item.id}">
                    ↩️
                </button>
            </div>
        `;
    });

    container.innerHTML = html || '<p class="text-gray-400 text-sm">Перетащите детали сюда</p>';

    // Добавляем обработчики для кнопок возврата
    container.querySelectorAll('.return-item').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            returnItemToOrder(btn.dataset.boxId, btn.dataset.itemId);
        });
    });
}
async function returnItemToOrder(boxId, boxItemId) {
    const orderId = document.querySelector('meta[name="order-id"]')?.content;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch(`/orders/${orderId}/boxes/${boxId}/items/${boxItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    });

    const data = await response.json();
    if (data.status === 'ok') {
        // Перезагружаем страницу для простоты (или можно динамически обновить)
        location.reload();
    }
}

// Экспорт
window.packing = {
    splitMode: () => splitMode,
    renderItemsList
};

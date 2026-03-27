// начальный индекс: считаем только реальные строки, без шаблона

let rowIndex = document.querySelectorAll('#order-items-body tr:not(#item-row-template)').length;

// Подсчёт заполненных полей в строке
function countFilledFields(row) {
    const fields = Array.from(row.querySelectorAll('input:not([type="file"]), select'));
    return fields.reduce((count, el) => {
        const val = (el.value || '').trim();

        if (!val) return count;

        if (el.name && el.name.includes('[thickness]') && val === '19') return count;

        if (el.tagName === 'SELECT') {
            const selectedOption = el.options[el.selectedIndex];
            if (selectedOption && (selectedOption.value === '' || selectedOption.text.trim() === '—')) return count;
        }

        return count + 1;
    }, 0);
}

// Добавление новой строки
function addRowIfValid(currentRow) {
    const filled = countFilledFields(currentRow);
    if (filled < 2) {
        currentRow.classList.add('ring-2', 'ring-red-400');
        setTimeout(() => currentRow.classList.remove('ring-2', 'ring-red-400'), 600);
        return null;
    }

    const tableBody = document.querySelector('#order-items-body');
    const templateRow = document.getElementById('item-row-template');
    const newRow = templateRow.cloneNode(true);

    Array.from(newRow.querySelectorAll('input, select')).forEach(function(el) {
        let name = el.getAttribute('name');
        if (!name) return;

        // Присваиваем индекс новой строке
        let newName = name.replace('__INDEX__', rowIndex);
        el.setAttribute('name', newName);
        el.removeAttribute('disabled');

        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else if (el.type !== 'file') {
            el.value = '';
        }

        if (el.type === 'file') el.value = '';
    });

    newRow.id = '';
    newRow.classList.remove('hidden');
    tableBody.appendChild(newRow);
    rowIndex++;

    setTimeout(() => {
        newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const firstControl = newRow.querySelector('input:not([type="file"]), select');
        if (firstControl) firstControl.focus({ preventScroll: true });
    }, 100);

    return newRow;
}

// Добавление строки по клику "+"
document.addEventListener('click', function (e) {
    const addButton = e.target.closest('.add-row');
    if (addButton) {
        const currentRow = addButton.closest('tr');
        const created = addRowIfValid(currentRow);
        if (created) {
            const firstControl = created.querySelector('input:not([type="file"]), select');
            if (firstControl) firstControl.focus();
        }
    }
});

// Удаление строки
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        const row = e.target.closest('tr');
        const rows = document.querySelectorAll('#order-items-body tr:not(#item-row-template)');
        if (rows.length > 1) {
            row.remove();
        }
    }
});

// Изменение кнопки при выборе файла
document.addEventListener('change', function (e) {
    if (e.target.type === 'file' && e.target.name.includes('[attachment]')) {
        const label = e.target.closest('label');
        const svgIcon = label.querySelector('svg');

        if (e.target.files.length > 0) {
            label.classList.remove('bg-blue-500');
            label.classList.add('bg-green-500');
            svgIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />`;
        } else {
            label.classList.remove('bg-green-500');
            label.classList.add('bg-blue-500');
            svgIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z" />`;
        }
    }
});

// Навигация клавишами
document.addEventListener('keydown', function (e) {
    const visibleInputs = Array.from(document.querySelectorAll('#order-items-table input:not([type="file"]), #order-items-table select'));
    const index = visibleInputs.indexOf(document.activeElement);

    const firstRow = document.querySelector('#order-items-body tr:not(#item-row-template)');
    const colsPerRow = firstRow ? firstRow.querySelectorAll('input:not([type="file"]), select').length : 0;

    if (e.key === 'Enter' || e.key === 'ArrowRight') {
        e.preventDefault();
        const name = document.activeElement.getAttribute('name');

        if (name && name.includes('[notes]')) {
            const plusButton = document.activeElement.closest('tr').querySelector('.add-row');
            if (plusButton) plusButton.focus();
            return;
        }

        const isAddButton = document.activeElement.classList.contains('add-row');
        if (isAddButton) {
            const currentRow = document.activeElement.closest('tr');
            const created = addRowIfValid(currentRow);
            if (created) {
                const firstControl = created.querySelector('input:not([type="file"]), select');
                if (firstControl) firstControl.focus();
            }
            return;
        }

        if (index >= 0 && index < visibleInputs.length - 1) {
            visibleInputs[index + 1].focus();
        }
    }

    if (e.key === 'ArrowLeft') {
        e.preventDefault();
        if (index > 0) visibleInputs[index - 1].focus();
    }

    if (e.key === 'ArrowUp' && colsPerRow > 0) {
        e.preventDefault();
        const prevIdx = index - colsPerRow;
        if (prevIdx >= 0) visibleInputs[prevIdx].focus();
    }

    if (e.key === 'ArrowDown' && colsPerRow > 0) {
        e.preventDefault();
        const nextIdx = index + colsPerRow;
        if (nextIdx < visibleInputs.length) visibleInputs[nextIdx].focus();
    }
});
//пересчет итогов
function recalcTotals() {
    // 1. Проверяем, есть ли вообще таблица с товарами на этой странице
    const tableBody = document.getElementById('order-items-body');
    if (!tableBody) return; // Если таблицы нет (мы на Index), просто выходим из функции

    let totalQty = 0;
    let totalSquare = 0;

    tableBody.querySelectorAll('tr:not(#item-row-template)').forEach(row => {
        const qty = parseFloat(row.querySelector('[name*="[quantity]"]')?.value) || 0;
        const h = parseFloat(row.querySelector('[name*="[height]"]')?.value) || 0;
        const w = parseFloat(row.querySelector('[name*="[width]"]')?.value) || 0;

        totalQty += qty;
        totalSquare += (h * w / 1_000_000) * qty;
    });

    // 2. Безопасно выводим итоги
    const qtyElem = document.getElementById('total-quantity');
    const squareElem = document.getElementById('total-square');

    if (qtyElem) qtyElem.textContent = totalQty;
    if (squareElem) squareElem.textContent = totalSquare.toFixed(2);
}

// Слушатель тоже лучше вешать только если мы на нужной странице
if (document.getElementById('order-items-body')) {
    document.addEventListener('input', recalcTotals);
}

window.updateStatus = function (orderId, statusId) {
    const tokenElement = document.querySelector('meta[name="csrf-token"]');
    if (!tokenElement) return;

    fetch(`/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenElement.content,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({status_id: statusId})
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const dateCell = document.getElementById(`date-status-${orderId}`);
                if (dateCell) dateCell.textContent = data.date_status;

                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    const statusMap = {
                        'new': ['bg-gray-300', 'text-gray-900'],
                        'received': ['bg-yellow-500', 'text-black'],
                        'in_progress': ['bg-blue-500', 'text-white'],
                        'ready': ['bg-green-500', 'text-white'],
                        'shipped': ['bg-green-300', 'text-gray-900'],
                        'completed': ['bg-purple-600', 'text-white'],
                        'cancelled': ['bg-red-500', 'text-white']
                    };
                    const allStatusClasses = Object.values(statusMap).flat();
                    row.classList.remove(...allStatusClasses, 'hover:bg-gray-50');

                    const newClasses = statusMap[data.status_key];
                    if (newClasses) {
                        row.classList.add(...newClasses, 'hover:bg-opacity-80');
                        const select = row.querySelector('select');
                        if (select) {
                            select.classList.remove(...allStatusClasses, 'bg-white');
                            select.classList.add(...newClasses);
                        }
                    }
                }
            }
        })
        .catch(err => console.error('Error:', err));
};

window.autoRefreshStatuses = function () {
    const rows = document.querySelectorAll('tr[data-order-id]');
    if (rows.length === 0) return;

    // Собираем все ID заказов со страницы в один массив
    const orderIds = Array.from(rows).map(row => row.getAttribute('data-order-id'));

    fetch('/api/orders/batch-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ ids: orderIds })
    })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                const statusMap = {
                    'new': ['bg-gray-300', 'text-gray-900'],
                    'received': ['bg-yellow-500', 'text-black'],
                    'in_progress': ['bg-blue-500', 'text-white'],
                    'ready': ['bg-green-500', 'text-white'],
                    'shipped': ['bg-green-300', 'text-gray-800'],
                    'completed': ['bg-purple-600', 'text-white'],
                    'cancelled': ['bg-red-500', 'text-white']
                };
                const allClasses = Object.values(statusMap).flat();

                // Пробегаемся по полученным данным и обновляем каждую строку
                Object.keys(response.orders).forEach(id => {
                    const data = response.orders[id];
                    const row = document.querySelector(`tr[data-order-id="${id}"]`);
                    if (!row) return;

                    // Обновляем текст и дату
                    const label = document.getElementById(`status-label-${id}`);
                    const date = document.getElementById(`date-status-${id}`);
                    if (label) label.textContent = data.label;
                    if (date) date.textContent = data.date_status;

                    // Обновляем цвета
                    row.classList.remove(...allClasses, 'bg-white', 'text-gray-900');
                    if (statusMap[data.status_key]) {
                        row.classList.add(...statusMap[data.status_key]);
                    }
                });
            }
        })
        .catch(err => console.error('Ошибка пачки:', err));
};
// ЗАПУСК ТАЙМЕРА
if (document.querySelector('tr[data-order-id]')) {
    setInterval(window.autoRefreshStatuses, 60000);
}

// resources/js/order.js

const updatePaintShop = (orderId, shopId) => {
    console.log('Меняем цех для заказа:', orderId, 'на цех ID:', shopId);

    fetch(`/orders/${orderId}/paint-shop`, { // ПРОВЕРЬТЕ: в роутах /admin или нет?
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ paint_shop_id: shopId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Сохранено в БД!');
            }
        })
        .catch(err => alert('Ошибка сохранения: ' + err));
};

// ЭТО КРИТИЧНО ДЛЯ VITE:
window.updatePaintShop = updatePaintShop;






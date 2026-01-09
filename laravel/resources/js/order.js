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

    Array.from(newRow.querySelectorAll('input, select')).forEach(el => {
        const name = el.getAttribute('name');
        if (name) {
            // заменяем __INDEX__ на реальный индекс
            el.setAttribute('name', name.replace('__INDEX__', rowIndex));
            el.removeAttribute('disabled');
            if (el.tagName.toLowerCase() === 'select') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        }
        if (el.type === 'file') el.value = '';
    });

    newRow.id = '';
    newRow.classList.remove('hidden');
    tableBody.appendChild(newRow);
    rowIndex++;

    newRow.scrollIntoView({ behavior: 'smooth', block: 'end' });
    const firstControl = newRow.querySelector('input:not([type="file"]), select');
    if (firstControl) firstControl.focus();

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
document.addEventListener('change', function(e) {
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
document.addEventListener('keydown', function(e) {
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

// Пересчёт итогов
function recalcTotals() {
    let totalQty = 0;
    let totalSquare = 0;

    document.querySelectorAll('#order-items-body tr:not(#item-row-template)').forEach(row => {
        const qty = parseFloat(row.querySelector('[name*="[quantity]"]')?.value) || 0;
        const h   = parseFloat(row.querySelector('[name*="[height]"]')?.value) || 0;
        const w   = parseFloat(row.querySelector('[name*="[width]"]')?.value) || 0;

        totalQty += qty;
        totalSquare += (h * w / 1_000_000) * qty;
    });

    document.getElementById('total-quantity').textContent = totalQty;
    document.getElementById('total-square').textContent   = totalSquare.toFixed(2);
}

document.addEventListener('input', recalcTotals);




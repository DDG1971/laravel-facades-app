
    let rowIndex = 1;

    // Count filled fields in a row (excluding defaults and empty values)
    function countFilledFields(row) {
    const fields = Array.from(row.querySelectorAll('input:not([type="file"]), select'));
    return fields.reduce((count, el) => {
    const val = (el.value || '').trim();

    // ignore truly empty
    if (!val) return count;

    // ignore default thickness "19"
    if (el.name && el.name.includes('[thickness]') && val === '19') return count;

    // ignore placeholder select with "—"
    if (el.tagName === 'SELECT') {
    const selectedOption = el.options[el.selectedIndex];
    if (selectedOption && (selectedOption.value === '' || selectedOption.text.trim() === '—')) return count;
}

    return count + 1;
}, 0);
}

    // Add a new row if valid; returns the created row or null
    function addRowIfValid(currentRow) {
    // Require at least 2 filled fields
    const filled = countFilledFields(currentRow);
    if (filled < 2) {
    // visual hint: briefly highlight row
    currentRow.classList.add('ring-2', 'ring-red-400');
    setTimeout(() => currentRow.classList.remove('ring-2', 'ring-red-400'), 600);
    return null;
}

    const tableBody = document.querySelector('#order-items-table tbody');
    const templateRow = tableBody.rows[0];
    const newRow = templateRow.cloneNode(true);

    Array.from(newRow.querySelectorAll('input, select')).forEach(el => {
    const name = el.getAttribute('name');
    if (name) {
    el.setAttribute('name', name.replace(/\d+/, rowIndex));
    if (el.tagName.toLowerCase() === 'select') {
    el.selectedIndex = 0;
} else {
    // clear text/number inputs
    el.value = '';
}
}
    // ensure file inputs are cleared
    if (el.type === 'file') el.value = '';
});

    tableBody.appendChild(newRow);
    rowIndex++;

    // Make the new row visible and focus first control
    newRow.scrollIntoView({ behavior: 'smooth', block: 'end' });
    const firstControl = newRow.querySelector('input:not([type="file"]), select');
    if (firstControl) firstControl.focus();

    return newRow;
}

    // Add row on click "+"
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

    // Remove row
    document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
    const row = e.target.closest('tr');
    const rows = document.querySelectorAll('#order-items-table tbody tr');
    if (rows.length > 1) {
    row.remove();
}
}
});

    // Make attachment button change on file selection (safe: preserve input)
    document.addEventListener('change', function(e) {
    if (e.target.type === 'file' && e.target.name.includes('attachment_path')) {
    const label = e.target.closest('label');
    const svgIcon = label.querySelector('svg');

    if (e.target.files.length > 0) {
    label.classList.remove('bg-blue-500');
    label.classList.add('bg-green-500');
    // checkmark icon
    svgIcon.setAttribute('viewBox', '0 0 24 24');
    svgIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                `;
} else {
    label.classList.remove('bg-green-500');
    label.classList.add('bg-blue-500');
    // paperclip icon
    svgIcon.setAttribute('viewBox', '0 0 24 24');
    svgIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z" />
                `;
}
}
});

    // Keyboard navigation with dynamic column count
    document.addEventListener('keydown', function(e) {
    const visibleInputs = Array.from(document.querySelectorAll('#order-items-table input:not([type="file"]), #order-items-table select'));
    const index = visibleInputs.indexOf(document.activeElement);

    // compute columns per row dynamically from first row
    const firstRow = document.querySelector('#order-items-table tbody tr');
    const colsPerRow = firstRow
    ? firstRow.querySelectorAll('input:not([type="file"]), select').length
    : 0;

    // Enter or ArrowRight
    if (e.key === 'Enter' || e.key === 'ArrowRight') {
    e.preventDefault();
    const name = document.activeElement.getAttribute('name');

    // notes -> focus "+"
    if (name && name.includes('[notes]')) {
    const plusButton = document.activeElement.closest('tr').querySelector('.add-row');
    if (plusButton) plusButton.focus();
    return;
}

    // if focus is on "+" (button is naturally focusable), add row and jump to first cell
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

    // default: move right
    if (index >= 0 && index < visibleInputs.length - 1) {
    visibleInputs[index + 1].focus();
}
}

    // ArrowLeft
    if (e.key === 'ArrowLeft') {
    e.preventDefault();
    if (index > 0) {
    visibleInputs[index - 1].focus();
}
}

    // ArrowUp: go to same column in previous row
    if (e.key === 'ArrowUp' && colsPerRow > 0) {
    e.preventDefault();
    const prevIdx = index - colsPerRow;
    if (prevIdx >= 0) visibleInputs[prevIdx].focus();
}

    // ArrowDown: go to same column in next row
    if (e.key === 'ArrowDown' && colsPerRow > 0) {
    e.preventDefault();
    const nextIdx = index + colsPerRow;
    if (nextIdx < visibleInputs.length) visibleInputs[nextIdx].focus();
}
});

    function recalcTotals() {
    let totalQty = 0;
    let totalSquare = 0;

    document.querySelectorAll('#order-items-table tbody tr').forEach(row => {
    const qty = parseFloat(row.querySelector('[name*="[quantity]"]')?.value) || 0;
    const h   = parseFloat(row.querySelector('[name*="[height]"]')?.value) || 0;
    const w   = parseFloat(row.querySelector('[name*="[width]"]')?.value) || 0;

    totalQty += qty;
    totalSquare += (h * w / 1_000_000) * qty; // площадь в м²
});

    document.getElementById('total-quantity').textContent = totalQty;
    document.getElementById('total-square').textContent   = totalSquare.toFixed(2);
}


    // пересчитывать при каждом изменении
    document.addEventListener('input', recalcTotals);


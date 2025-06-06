<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
let lineIndex = 0;
let products = @json($products);

function addLine(data = {}) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="handle" style="cursor: move;">↕</td>
        <td>
            <select name="lines[${lineIndex}][product_id]" class="form-select">
                ${products.map(p => `<option value="${p.id}" data-unit="${p.unit_type}" data-price="${p.unit_price}" ${p.id == data.product_id ? 'selected' : ''}>${p.name}</option>`).join('')}
            </select>
            <input type="hidden" name="lines[${lineIndex}][unit_type]" value="${data.unit_type || ''}">
        </td>
        <td><input type="number" step="0.01" name="lines[${lineIndex}][length]" value="${data.length || ''}" class="form-control"></td>
        <td><input type="number" step="0.01" name="lines[${lineIndex}][width]" value="${data.width || ''}" class="form-control"></td>
        <td><input type="number" step="0.01" name="lines[${lineIndex}][quantity]" value="${data.quantity || ''}" class="form-control"></td>
        <td><input type="number" step="0.01" name="lines[${lineIndex}][unit_price]" value="${data.unit_price || ''}" class="form-control"></td>
        <td><input type="number" step="0.01" name="lines[${lineIndex}][line_total]" value="${data.line_total || ''}" class="form-control" readonly></td>
        <td>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success" onclick="copyLine(this)">نسخ</button>
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('tr').remove(); calculateTotals();">حذف</button>
            </div>
        </td>
    `;
    document.getElementById('lines-body').appendChild(row);

    const select = $(row).find('select');

    select.select2({ width: '100%' });

    select.on('select2:open', () => {
        setTimeout(() => {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });

    select.on('focus', function () {
        $(this).select2('open');
    });

    select.on('change', function () {
        updateUnit(this, lineIndex);
    });

    $(row).find('input').on('input', function () {
        calculateLine(lineIndex);
    });

    updateUnit(row.querySelector('select'), lineIndex);
    lineIndex++;
}

function copyLine(button) {
    const sourceRow = button.closest('tr');
    const data = {};
    sourceRow.querySelectorAll('input, select').forEach(input => {
        const name = input.name;
        const value = input.value;
        if (name.includes('[product_id]')) data.product_id = value;
        if (name.includes('[length]')) data.length = value;
        if (name.includes('[width]')) data.width = value;
        if (name.includes('[quantity]')) data.quantity = value;
        if (name.includes('[unit_price]')) data.unit_price = value;
        if (name.includes('[line_total]')) data.line_total = value;
        if (name.includes('[unit_type]')) data.unit_type = value;
    });
    addLine(data);
}

function updateUnit(select, index) {
    const selected = select.options[select.selectedIndex];
    const unitType = selected.dataset.unit;
    const price = selected.dataset.price;
    select.parentElement.querySelector('input[type="hidden"]').value = unitType;
    select.closest('tr').querySelector(`input[name="lines[${index}][unit_price]"]`).value = price;
    calculateLine(index);
}

function calculateLine(index) {
    const row = document.querySelector(`input[name="lines[${index}][quantity]"]`)?.closest('tr');
    if (!row) return;
    const unit = row.querySelector(`input[name="lines[${index}][unit_type]"]`).value;
    const length = parseFloat(row.querySelector(`input[name="lines[${index}][length]"]`).value) || 1;
    const width = parseFloat(row.querySelector(`input[name="lines[${index}][width]"]`).value) || 1;
    const qty = parseFloat(row.querySelector(`input[name="lines[${index}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="lines[${index}][unit_price]"]`).value) || 0;

    let total = unit === 'متر مربع' ? length * width * qty * price : qty * price;
    row.querySelector(`input[name="lines[${index}][line_total]"]`).value = total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let total = 0;
    document.querySelectorAll('input[name$="[line_total]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const tax = ((total - discount) * 0.05).toFixed(2);
    const grand = total - discount + parseFloat(tax);

    document.querySelector('input[name="total_amount"]').value = total.toFixed(2);
    document.querySelector('input[name="tax_amount"]').value = tax;
    document.querySelector('input[name="grand_total"]').value = grand.toFixed(2);
}

document.addEventListener("DOMContentLoaded", () => {
    for (let i = 0; i < 8; i++) addLine();

    Sortable.create(document.getElementById('lines-body'), {
        animation: 150,
        handle: '.handle',
        ghostClass: 'bg-light'
    });
});

document.addEventListener('keydown', function(e) {
    const isInput = e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT';
    if (isInput && e.key === 'Enter') {
        const name = e.target.name;
        if (name.includes('[length]') || name.includes('[width]') || name.includes('[quantity]') || name.includes('[unit_price]')) {
            e.preventDefault();
        }
    }
});
</script>

<style>
.summary-box {
    position: sticky;
    top: 20px;
    min-width: 250px;
}
</style>

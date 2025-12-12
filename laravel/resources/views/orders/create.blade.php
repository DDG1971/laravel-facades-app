@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Новый заказ</h2>

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <!-- Шапка заказа -->
            <div class="mb-3">
                <label for="customer_id">Клиент</label>
                <select name="customer_id" id="customer_id" class="form-select">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="status_id">Статус заказа</label>
                <select name="status_id" id="status_id" class="form-select">
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>

            <hr>

            <!-- Таблица позиций -->
            <h4>Позиции заказа</h4>
            <table class="table" id="order-items-table">
                <thead>
                <tr>
                    <th>Тип фасада</th>
                    <th>Материал</th>
                    <th>Толщина</th>
                    <th>Высота</th>
                    <th>Ширина</th>
                    <th>Количество</th>
                    <th>Цвет</th>
                    <th>Сверловка</th>
                    <th>Примечания</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <select name="items[0][facade_type_id]" class="form-select">
                            @foreach($facadeTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="items[0][material]" class="form-control"></td>
                    <td><input type="number" name="items[0][thickness]" class="form-control"></td>
                    <td><input type="number" name="items[0][height]" class="form-control"></td>
                    <td><input type="number" name="items[0][width]" class="form-control"></td>
                    <td><input type="number" name="items[0][quantity]" class="form-control"></td>
                    <td>
                        <select name="items[0][color_code_id]" class="form-select">
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->code }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="items[0][drilling_id]" class="form-select">
                            @foreach($drillings as $drilling)
                                <option value="{{ $drilling->id }}">{{ $drilling->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="items[0][notes]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger remove-row">Удалить</button></td>
                </tr>
                </tbody>
            </table>

            <button type="button" id="add-row" class="btn btn-secondary">Добавить позицию</button>

            <hr>

            <button type="submit" class="btn btn-primary">Сохранить заказ</button>
        </form>
    </div>

    <script>
        let rowIndex = 1;
        document.getElementById('add-row').addEventListener('click', function() {
            const tableBody = document.querySelector('#order-items-table tbody');
            const newRow = tableBody.rows[0].cloneNode(true);

            // Обновляем имена полей
            Array.from(newRow.querySelectorAll('input, select')).forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\d+/, rowIndex));
                    el.value = '';
                }
            });

            tableBody.appendChild(newRow);
            rowIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('#order-items-table tbody tr').length > 1) {
                    row.remove();
                }
            }
        });
    </script>
@endsection

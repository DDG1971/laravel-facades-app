<div class="grid grid-cols-4 divide-x divide-y border border-gray-400 w-fit text-sm">
    <!-- Дата создания -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Дата создания</label>
        <input
            type="date"
            class="w-48 border rounded-md px-2 py-1 text-sm bg-gray-100"
            value="{{ $order->created_at->toDateString() }}"
            readonly
        >
    </div>

    <!-- Клиент -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Клиент</label>
        <select class="w-64 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->customer->company_name ?? '—' }}</option>
        </select>
    </div>

    <!-- № заказа клиента -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">№ заказа клиента</label>
        <input
            type="text"
            class="w-64 border rounded-md px-2 py-1 text-sm bg-gray-100"
            value="{{ $order->client_order_number }}"
            readonly
        >
    </div>

    <!-- Каталог цветов -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Каталог цветов</label>
        <select class="w-48 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->colorCatalog->name_en ?? '—' }}</option>
        </select>
    </div>

    <!-- Код цвета -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Код цвета</label>
        <select class="w-64 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->colorCode->code ?? '—' }}</option>
        </select>
    </div>

    <!-- Тип покрытия -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Тип покрытия</label>
        <select class="w-64 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->coatingType->label ?? '—' }}</option>
        </select>
    </div>

    <!-- Материал -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Материал</label>
        <select class="w-48 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->material }}</option>
        </select>
    </div>

    <!-- Фрезеровка -->
    <div class="p-2">
        <label class="block text-xs font-medium text-gray-700">Фрезеровка</label>
        <select class="w-64 border rounded-md px-2 py-1 bg-gray-100 text-sm" disabled>
            <option>{{ $order->milling->name ?? '—' }}</option>
        </select>
    </div>
</div>


<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Список заказов
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-6 lg:px-8">
            <!-- фиксированный хедер -->
            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-separate text-sm">
                    <thead class="bg-gray-100 shadow-sm">
                    <tr>
                        <th class="border px-2 py-1">Очер.</th>
                        <th class="border px-2 py-1">Клиент</th>
                        <th class="border px-2 py-1">№ клиента</th>
                        <th class="border px-2 py-1">Дата</th>
                        <th class="border px-2 py-1">Мат-лы</th>
                        <th class="border px-2 py-1">Каталог</th>
                        <th class="border px-2 py-1">№ цвета</th>
                        <th class="border px-2 py-1">Покрыт.</th>
                        <th class="border px-2 py-1">Фрезеровка</th>
                        <th class="border px-2 py-1">м²</th>
                        <th class="border px-2 py-1">Статус</th>
                        <th class="border px-2 py-1">Дата статуса</th>
                        <th class="border px-2 py-1">Цех №</th>
                        <th class="border px-2 py-1">Действ</th>
                    </tr>
                    </thead>
                </table>
            </div>


            <!-- тело -->
            <div class="overflow-x-auto overflow-y-auto h-[70vh]">
                <table class="w-full table-fixed border-separate text-sm">
                    <tbody>
                    @forelse($orders as $order)
                        <<tr data-order-id="{{ $order->id }}"
                             class="transition-colors duration-300 hover:bg-gray-50
                              @switch($order->status->name)
                               @case('new') bg-blue-100 text-blue-800 @break
                                @case('received') bg-yellow-100 text-yellow-800 @break
                                 @case('in_progress') bg-indigo-300 text-indigo-900 @break
                                  @case('paint_shop') bg-purple-100 text-purple-800 @break
                                   @case('ready') bg-green-100 text-green-800 @break
                                    @case('shipped') bg-teal-100 text-teal-800 @break
                                     @case('completed') bg-gray-200 text-gray-800 @break
                                      @case('cancelled') bg-red-100 text-red-800 @break
                                       @endswitch">
                            <!-- Очередь -->
                            <td class="border px-2 py-1 text-center">{{ $order->queue_number }}</td>
                            <!-- Клиент -->
                            <td class="border px-2 py-1 truncate overflow-hidden whitespace-nowrap">
                                {{ $order->customer->company_name ?? 'нет данных' }}
                            </td>
                            <!-- № клиента -->
                            <td class="border px-2 py-1 text-center">
                                {{ $order->client_order_number }}
                            </td>
                            <!-- Дата получения -->
                            <td class="border px-2 py-1 text-center">
                                {{ $order->date_received ? \Carbon\Carbon::parse($order->date_received)->format('d.m.Y') : '—' }}
                            </td>
                            <!-- Материал -->
                            <td class="border px-2 py-1">{{ $order->material }}</td>
                            <!-- Каталог -->
                            <td class="border px-2 py-1">{{ $order->colorCatalog->name_en ?? '—' }}</td>
                            <!-- Код цвета -->
                            <td class="border px-2 py-1 text-center">{{ $order->colorCode->code ?? '—' }}</td>
                            <!-- Покрытие -->
                            <td class="border px-2 py-1">{{ $order->coatingType->name ?? '—' }}</td>
                            <!-- Фрезеровка -->
                            <td class="border px-2 py-1">{{ $order->milling->name ?? '—' }}</td>
                            <!-- Площадь -->
                            <td class="border px-2 py-1 text-center">{{ $order->square_meters }}</td>
                            <!-- Статус -->
                            <td class="border px-2 py-1 text-center">
                                <select onchange="updateStatus({{ $order->id }}, this.value)"
                                        class="text-sm border rounded px-1 py-0.5 focus:outline-none focus:ring focus:ring-blue-300">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" @selected($order->status_id == $status->id)>
                                            {{ $status->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Дата статуса -->
                            <td id="date-status-{{ $order->id }}" class="border px-2 py-1 text-center">
                                {{ $order->date_status ? \Carbon\Carbon::parse($order->date_status)->format('d.m.Y') : '—' }}
                            </td>
                            <!-- Цех -->
                            <td class="border px-2 py-1 text-center">
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                                    <a href="{{ route('orders.manage', $order->id) }}"
                                       class="px-2 py-1 underline hover:font-bold">
                                        ⚙ Управление
                                    </a>
                                @endif
                            </td>
                            <!-- Действия -->
                            <td class="border px-2 py-1 space-x-2 text-center">
                                <a href="{{ route('orders.preview', $order) }}"
                                   class="text-blue-600 hover:underline"
                                   title="Печать">
                                    🖨️
                                </a>
                                <a href="{{ route('orders.edit', $order) }}"
                                   class="text-green-600 hover:underline" title="Редактировать">
                                    ✏️
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="border px-3 py-4 text-center text-gray-500">
                                Заказов пока нет
                            </td>
                        </tr>
                    @endforelse
                    <!-- 🔹 Тестовая строка -->
                    <tr data-order-id="999" class="bg-blue-100 text-blue-800">
                        <td class="border px-2 py-1 text-center">999</td>
                        <td class="border px-2 py-1">Тестовый клиент</td>
                        <td class="border px-2 py-1 text-center">TST-001</td>
                        <td class="border px-2 py-1 text-center">06.02.2026</td>
                        <td class="border px-2 py-1">MDF</td>
                        <td class="border px-2 py-1">Каталог</td>
                        <td class="border px-2 py-1 text-center">C01</td>
                        <td class="border px-2 py-1">Покрытие</td>
                        <td class="border px-2 py-1">Фрезеровка</td>
                        <td class="border px-2 py-1 text-center">10</td>
                        <td class="border px-2 py-1 text-center">
                            <select onchange="updateStatus(999, this.value)">
                                <option value="1">Новый</option>
                                <option value="2">В работе</option>
                                <option value="3">Готов</option>
                            </select>
                        </td>
                        <td id="date-status-999" class="border px-2 py-1 text-center">—</td>
                        <td class="border px-2 py-1 text-center">Цех</td>
                        <td class="border px-2 py-1 text-center">⚙</td>
                        <td class="border px-2 py-1 text-center">✏️</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

                    <script>
                        const statusClasses = {
                            new: ['bg-blue-100','text-blue-800'],
                            in_progress: ['bg-indigo-300','text-indigo-900'],
                            ready: ['bg-green-100','text-green-800']
                        };

                        window.updateStatus = function(orderId, statusId) {
                            console.log('updateStatus вызван', orderId, statusId);

                            const fakeResponse = {
                                success: true,
                                status_key: statusId == 1 ? 'new' : statusId == 2 ? 'in_progress' : 'ready',
                                date_status: new Date().toLocaleDateString('ru-RU')
                            };

                            if (fakeResponse.success) {
                                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                                console.log('row найден?', row);

                                if (row) {
                                    Object.values(statusClasses).flat().forEach(cls => row.classList.remove(cls));
                                    statusClasses[fakeResponse.status_key].forEach(cls => row.classList.add(cls));
                                }

                                const dateCell = document.querySelector(`#date-status-${orderId}`);
                                if (dateCell) {
                                    dateCell.textContent = fakeResponse.date_status;
                                }
                            }
                        };
                    </script>
</x-app-layout>

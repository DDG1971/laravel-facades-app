<x-app-layout>
    <x-slot name="head">
        <!-- 1. Подключаем ваши рабочие стили через ваш компонент -->
        <x-assets />

        <!-- 2. Подключаем библиотеку графиков -->
         <script src="{{ asset('js/chart.min.js') }}"></script>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Детальная статистика за {{ $startOfMonth->translatedFormat('F Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- 1. ФОРМА ФИЛЬТРА -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <form action="{{ route('admin.statistics') }}" method="GET" class="bg-white p-4 rounded-lg shadow flex flex-wrap items-end gap-4 border border-gray-100">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Месяц</label>
                    <select name="month" class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Год</label>
                    <select name="year" class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(now()->year, now()->year - 2) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition font-medium">Показать</button>
                <a href="{{ route('admin.statistics') }}" class="text-gray-500 text-sm hover:underline pb-2">Сбросить</a>
            </form>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 2. ВЕРХНИЕ КАРТОЧКИ (4 колонки) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-500">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Принято (м²)</p>
                    <p class="text-2xl font-black text-gray-800">{{ number_format($totalM2, 2) }} м²</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-yellow-500">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Сумма заказов</p>
                    <p class="text-2xl font-black text-yellow-600">{{ number_format($totalMoney, 0, '.', ' ') }} $</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-green-500">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Оплачено (Факт)</p>
                    <p class="text-2xl font-black text-green-600">{{ number_format($actuallyPaid, 0, '.', ' ') }} $</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-red-500">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Дебиторка</p>
                    <p class="text-2xl font-black text-red-600">{{ number_format($debt, 0, '.', ' ') }} $</p>
                </div>
            </div>

            <!-- 3. ГРАФИКИ (3 колонки: 1 + 2) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Круговая диаграмма -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-gray-500 font-bold mb-4 text-center uppercase text-xs tracking-wider">Доля цехов (м²)</h3>
                    <canvas id="workshopChart" height="250"></canvas>
                </div>
                <!-- Линейный график -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <h3 class="text-gray-500 font-bold mb-4 uppercase text-xs tracking-wider text-left">График приема заказов (м²)</h3>
                    <div style="height: 250px;"><canvas id="dailyChart"></canvas></div>
                </div>
            </div>

            <!-- 4. ТАБЛИЦЫ ЦЕХОВ (2 колонки) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($statsByWorkshop as $shopId => $items)
                    @php
                        // Берем имя цеха из первой позиции в группе
                        $shopName = $items->first()->workshop_name ?? 'Не распределено';
                    @endphp

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
                            <h3 class="text-lg font-bold">Цех: {{ $shopName }}</h3>
                            <span class="bg-blue-600 px-3 py-1 rounded text-sm font-bold">
                    {{ number_format($items->sum('row_square'), 2) }} м²
                </span>
                        </div>

                        <div class="p-4">
                            <table class="w-full text-sm text-left font-sans">
                                <thead class="text-gray-400 border-b">
                                <tr>
                                    <th class="pb-2 font-medium">Фрезеровка (Деталь)</th>
                                    <th class="pb-2 text-center font-medium uppercase text-[10px]">Фасадов (шт)</th>
                                    <th class="pb-2 text-right font-medium uppercase text-[10px]">Квадратура</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items->groupBy('stat_name') as $name => $millingRows)
                                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                                        <td class="py-3 font-medium text-gray-700">
                                            {{ $name }}
                                        </td>
                                        {{-- Добавляем колонку с общим количеством штук --}}
                                        <td class="py-3 text-center text-gray-600 font-semibold">
                                            {{ $millingRows->sum('quantity') }}
                                        </td>
                                        <td class="py-3 text-right font-bold text-gray-900">
                                            {{ number_format($millingRows->sum('row_square'), 2) }} м²
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ИНИЦИАЛИЗАЦИЯ СКРИПТОВ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Круговая диаграмма
            const ctxW = document.getElementById('workshopChart').getContext('2d');
            new Chart(ctxW, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } } }
                }
            });

            // 2. Линейный график
            const ctxD = document.getElementById('dailyChart').getContext('2d');
            new Chart(ctxD, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyLabels) !!},
                    datasets: [{
                        label: 'м²',
                        data: {!! json_encode($dailyData) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#3B82F6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</x-app-layout>


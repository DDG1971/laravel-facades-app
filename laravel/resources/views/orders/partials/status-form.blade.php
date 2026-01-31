<form method="POST" action="{{ route('orders.updateStatus', $order->id) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label for="status_id" class="block text-sm font-medium text-gray-700">Текущий статус</label>
        <select id="status_id" name="status_id"
                class="mt-1 block w-64 border rounded-md px-2 py-1 text-sm bg-white">
            @foreach($statuses as $status)
                <option value="{{ $status->id }}"
                    {{ $order->status_id == $status->id ? 'selected' : '' }}>
                    {{ $status->label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex space-x-4">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Обновить статус
        </button>

        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Назад к списку
            </a>
        @else
            <a href="{{ route('orders.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Назад к списку
            </a>
        @endif
    </div>
</form>

<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Clients') }}
        </h2>
        <x-primary-button onclick="window.location='{{ url('/admin/dashboard') }}'">
            {{ __('Back to Dashboard') }}
        </x-primary-button>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Company</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td class="px-4 py-2">{{ $client->id }}</td>
                    <td class="px-4 py-2">{{ $client->company_name }}</td>
                    <td class="px-4 py-2">{{ $client->phone }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ url('/admin/clients/'.$client->id.'/edit') }}"
                           class="text-blue-600">Редактировать</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>

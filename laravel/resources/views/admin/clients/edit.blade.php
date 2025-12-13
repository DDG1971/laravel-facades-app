<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Edit Client') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 mt-6">
        <form method="POST" action="{{ url('/admin/clients/'.$client->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700">Contact Name</label>
                <input type="text" name="contact_person" value="{{ $client->contact_person }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Company Name</label>
                <input type="text" name="company_name" value="{{ $client->company_name }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ $client->phone }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Address</label>
                <input type="text" name="address" value="{{ $client->address }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Contract Number</label>
                <input type="text" name="contract_number" value="{{ $client->contract_number }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <!-- можно добавить другие поля: ИНН, email, комментарии -->

            <x-primary-button>
                {{ __('Save Changes') }}
            </x-primary-button>
        </form>
    </div>
</x-app-layout>

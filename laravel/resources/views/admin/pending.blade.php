<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Pending Users') }}
        </h2>
        <x-primary-button onclick="window.location='{{ url('/admin/dashboard') }}'">
            {{ __('Back to Dashboard') }}
        </x-primary-button>

    </x-slot>

    @if (session('status'))
        <div class="mb-4 text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <table class="min-w-full divide-y divide-gray-200 mt-4">
        <thead>
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr>
                <td class="px-4 py-2">{{ $user->id }}</td>
                <td class="px-4 py-2">{{ $user->name }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2">

                    <form method="POST" action="{{ url('/admin/approve/'.$user->id) }}">
                    {{--<form method="POST" action="{{ route('admin.approve', $user) }}">--}}
                        @csrf
                        <x-primary-button>
                            {{ __('Approve') }}
                        </x-primary-button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-app-layout>

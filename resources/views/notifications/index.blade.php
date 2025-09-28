{{-- notification.index --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Notifications</h1>

        @if (isset($error))
            <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-lg mb-6">
                {{ $error }}
            </div>
        @endif

        @if ($alerts->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <form action="{{ route('alerts.dismiss-all') }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition duration-200">
                        Dismiss All
                    </button>
                </form>

                <ul class="space-y-3">
                    @foreach ($alerts as $alert)
                        <li class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div class="flex justify-between items-center">
                                <a href="{{ $alert->url ?? '#' }}" class="text-{{ $alert->type === 'critical' ? 'red' : ($alert->type === 'warning' ? 'yellow' : ($alert->type === 'success' ? 'green' : 'blue')) }}-600 dark:text-{{ $alert->type === 'critical' ? 'red' : ($alert->type === 'warning' ? 'yellow' : ($alert->type === 'success' ? 'green' : 'blue')) }}-400 hover:underline">
                                    {{ $alert->message }}
                                </a>
                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="inline"> <!-- Updated to use $alert (model binding) -->
                                    @csrf
                                    <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Read</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{ $alerts->links() }}
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 italic text-center py-4">No notifications available.</p>
        @endif
    </div>
@endsection
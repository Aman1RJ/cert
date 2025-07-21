@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Events</h1>
        <a href="{{ route('events.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors">
            Create Event
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="divide-y divide-gray-200">
            @forelse($events as $event)
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                <a href="{{ route('events.show', $event) }}" class="hover:text-primary-600">
                                    {{ $event->name }}
                                </a>
                            </h3>
                            @if($event->description)
                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($event->description, 100) }}</p>
                            @endif
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                <span>{{ $event->event_date->format('M j, Y') }}</span>
                                <span>{{ $event->participants_count ?? 0 }} participants</span>
                                <span>{{ $event->certificates_count ?? 0 }} certificates</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($event->status) }}
                            </span>
                            <a href="{{ route('events.edit', $event) }}" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M9 22c0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.19-.274 2.314-.769 3.313l13.063 13.063L36 39.688 22.938 26.625C21.939 27.12 20.815 27.394 19.625 27.394" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No events</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new event.</p>
                    <div class="mt-6">
                        <a href="{{ route('events.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors">
                            Create Event
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'Reminders')
@section('header', 'Reminders')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/30">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                <i class="fas fa-bullhorn text-blue-600 dark:text-blue-400 mr-2"></i> Announcements & Reminders
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($announcements as $ann)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex gap-3">
                    <!-- Icon with dark mode -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 
                        @if($ann->type == 'info') bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400
                        @elseif($ann->type == 'warning') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400
                        @elseif($ann->type == 'success') bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400
                        @else bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 @endif">
                        <i class="fas 
                            @if($ann->type == 'info') fa-info-circle
                            @elseif($ann->type == 'warning') fa-exclamation-triangle
                            @elseif($ann->type == 'success') fa-check-circle
                            @else fa-times-circle @endif text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start flex-wrap gap-1">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $ann->title }}</h4>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $ann->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $ann->content }}</p>
                        @if($ann->start_date || $ann->end_date)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $ann->start_date ? \Carbon\Carbon::parse($ann->start_date)->format('M d, Y') : 'Always' }} - 
                                {{ $ann->end_date ? \Carbon\Carbon::parse($ann->end_date)->format('M d, Y') : 'Always' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <i class="far fa-newspaper text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                <p>No announcements at this time</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
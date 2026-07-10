@extends('layouts.admin')

@section('title', 'Notifications')
@section('header', 'All Notifications')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-bell text-blue-600 mr-2"></i> All Notifications
        </h3>
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-check-double mr-1"></i> Mark all as read
            </button>
        </form>
    </div>
    
    <div class="divide-y divide-gray-100">
        @forelse($notifications as $notification)
        <div class="p-4 hover:bg-gray-50 transition {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                    @if($notification->type == 'new_student') bg-green-100 text-green-600
                    @elseif($notification->type == 'new_request') bg-yellow-100 text-yellow-600
                    @else bg-blue-100 text-blue-600 @endif">
                    @if($notification->type == 'new_student')
                        <i class="fas fa-user-graduate"></i>
                    @elseif($notification->type == 'new_request')
                        <i class="fas fa-ticket-alt"></i>
                    @else
                        <i class="fas fa-bell"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $notification->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('admin.notifications.mark-read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Mark as read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-bell-slash text-4xl mb-2 text-gray-300"></i>
            <p>No notifications yet</p>
        </div>
        @endforelse
    </div>
    
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
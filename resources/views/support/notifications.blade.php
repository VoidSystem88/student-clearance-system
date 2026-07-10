@extends('layouts.support')

@section('title', 'All Notifications')
@section('header', 'All Notifications')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-bell text-blue-600 mr-2"></i> All Notifications
        </h3>
        <button onclick="markAllSupportNotificationsAsRead()" class="text-sm text-blue-600 hover:text-blue-800 transition">
            <i class="fas fa-check-double mr-1"></i> Mark all as read
        </button>
    </div>
    
    <div class="divide-y divide-gray-100">
        @php
            $allNotifications = collect();
            
            foreach($bugReports as $bug) {
                $allNotifications->push([
                    'id' => 'bug_' . $bug->id,
                    'type' => 'bug_report',
                    'title' => '🐛 Bug Report',
                    'message' => ($bug->name ?? 'Anonymous') . ' reported: ' . ucfirst(str_replace('_', ' ', $bug->type)),
                    'created_at' => $bug->created_at,
                    'link' => route('support.dashboard') . '#bug-reports',
                    'icon' => 'fa-bug',
                    'color' => 'red',
                    'status' => $bug->status
                ]);
            }
            
            foreach($supportRequests as $req) {
                $allNotifications->push([
                    'id' => 'request_' . $req->id,
                    'type' => 'support_request',
                    'title' => '🎫 Support Request',
                    'message' => ($req->student->first_name ?? 'Student') . ' needs assistance: ' . ucfirst(str_replace('_', ' ', $req->request_type)),
                    'created_at' => $req->created_at,
                    'link' => route('support.requests'),
                    'icon' => 'fa-ticket-alt',
                    'color' => 'blue',
                    'status' => $req->status
                ]);
            }
            
            foreach($feedbacks as $fb) {
                $allNotifications->push([
                    'id' => 'feedback_' . $fb->id,
                    'type' => 'feedback',
                    'title' => '⭐ New Feedback',
                    'message' => ($fb->user->first_name ?? 'Student') . ' gave ' . $fb->rating . '⭐ feedback',
                    'created_at' => $fb->created_at,
                    'link' => route('support.feedbacks'),
                    'icon' => 'fa-star',
                    'color' => 'purple',
                    'status' => $fb->status
                ]);
            }
            
            $sortedNotifications = $allNotifications->sortByDesc('created_at');
        @endphp
        
        @forelse($sortedNotifications as $notif)
        @php
            $colorClass = $notif['color'] === 'red' ? 'bg-red-100 text-red-600' :
                          ($notif['color'] === 'purple' ? 'bg-purple-100 text-purple-600' :
                          'bg-blue-100 text-blue-600');
            $viewedKey = 'support_viewed_' . $notif['type'] . '_' . $notif['id'];
            $isUnread = session($viewedKey) === null;
            
            $statusBadge = '';
            if ($notif['status'] ?? false && $notif['status'] === 'pending') {
                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 ml-2">
                    <i class="fas fa-clock text-xs"></i> Pending
                </span>';
            }
        @endphp
        <div class="p-4 hover:bg-gray-50 transition {{ $isUnread ? 'bg-blue-50' : '' }}">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center">
                        <i class="fas {{ $notif['icon'] }}"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $notif['title'] }} {!! $statusBadge !!}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notif['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</p>
                        </div>
                        <a href="{{ $notif['link'] }}" class="text-sm text-blue-600 hover:text-blue-800 transition whitespace-nowrap ml-4">
                            View →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-bell-slash text-4xl mb-2 text-gray-300"></i>
            <p>No notifications yet</p>
            <p class="text-sm text-gray-400 mt-1">All caught up! 🎉</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function markAllSupportNotificationsAsRead() {
    fetch('{{ route("support.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear session storage items
            const keys = Object.keys(sessionStorage);
            keys.forEach(key => {
                if (key.startsWith('support_viewed_')) {
                    sessionStorage.removeItem(key);
                }
            });
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
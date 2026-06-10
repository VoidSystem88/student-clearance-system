@extends('layouts.support')

@section('title', 'Announcements')

@section('header', 'System Announcements')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-5 border-b pb-3">
        <h3 class="text-xl font-bold text-gray-800">
            <i class="fas fa-bullhorn text-blue-600 mr-2"></i>
            System Announcements & Broadcast
        </h3>
        <button id="createAnnouncementBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
            <i class="fas fa-plus mr-1"></i> New Announcement
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Send Update / Notice Card -->
        <div class="border border-gray-200 rounded-xl p-5 bg-gradient-to-br from-blue-50 to-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope-open-text text-blue-600"></i>
                </div>
                <h4 class="font-bold text-gray-800">Send Update Notice to All Users</h4>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Notify all students and staff about new features, policy changes, or general system updates via email.
            </p>
            <form id="updateAnnouncementForm" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="general_update">
                <textarea name="message" rows="3" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="e.g., We've added a new document upload feature. Please check your clearance status." required></textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm w-full transition flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Send Email to All
                </button>
            </form>
        </div>

        <!-- Temporary Shutdown / System Maintenance Card -->
        <div class="border border-gray-200 rounded-xl p-5 bg-gradient-to-br from-red-50 to-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-power-off text-red-600"></i>
                </div>
                <h4 class="font-bold text-gray-800">Temporary System Shutdown</h4>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Schedule maintenance or temporary downtime. An email alert will be sent to all users immediately.
            </p>
            <form id="shutdownForm" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="shutdown">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Shutdown Reason / Message</label>
                    <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm" 
                        placeholder="e.g., System will be offline on Sunday 2 AM - 6 AM for maintenance" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Start Time</label>
                        <input type="datetime-local" name="start_time" class="w-full border rounded p-1.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">End Time (optional)</label>
                        <input type="datetime-local" name="end_time" class="w-full border rounded p-1.5 text-sm">
                    </div>
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm w-full transition flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Broadcast Shutdown & Email Alert
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Announcements History -->
    <div class="mt-8">
        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
            <i class="fas fa-history mr-2 text-gray-500"></i> 
            Recent Broadcasts
        </h4>
        <div id="announcementHistoryList" class="space-y-2 max-h-64 overflow-y-auto">
            <div class="text-gray-400 text-sm italic text-center py-4">
                <i class="fas fa-inbox mr-2"></i> No recent announcements. Send a broadcast above.
            </div>
        </div>
        <div class="mt-3 text-right">
            <button id="clearHistoryBtn" class="text-xs text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-trash-alt mr-1"></i> Clear History
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Load history from localStorage
    function loadHistory() {
        const history = JSON.parse(localStorage.getItem('announcement_history') || '[]');
        const container = document.getElementById('announcementHistoryList');
        if (history.length === 0) {
            container.innerHTML = '<div class="text-gray-400 text-sm italic text-center py-4"><i class="fas fa-inbox mr-2"></i> No recent announcements. Send a broadcast above.</div>';
            return;
        }
        container.innerHTML = history.map(item => `
            <div class="border-l-4 ${item.type === 'shutdown' ? 'border-red-400 bg-red-50' : 'border-blue-400 bg-gray-50'} p-3 rounded-r-lg shadow-sm text-sm hover:shadow transition">
                <div class="flex justify-between items-start">
                    <span class="font-semibold ${item.type === 'shutdown' ? 'text-red-700' : 'text-blue-700'}">
                        <i class="fas ${item.type === 'shutdown' ? 'fa-power-off' : 'fa-envelope'} mr-1"></i>
                        ${item.type === 'shutdown' ? '⚠️ SYSTEM SHUTDOWN NOTICE' : '📢 SYSTEM UPDATE'}
                    </span>
                    <span class="text-xs text-gray-400">${new Date(item.createdAt).toLocaleString()}</span>
                </div>
                <p class="text-gray-700 mt-1 text-xs">${escapeHtml(item.message.substring(0, 150))}${item.message.length > 150 ? '...' : ''}</p>
                ${item.metadata?.start_time ? `
                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                        <i class="far fa-clock"></i> 
                        Scheduled: ${new Date(item.metadata.start_time).toLocaleString()}
                        ${item.metadata.end_time ? ' → ' + new Date(item.metadata.end_time).toLocaleString() : ''}
                    </div>
                ` : ''}
            </div>
        `).join('');
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    
    // Send announcement function
    async function sendAnnouncement(message, type, metadata = {}) {
        Swal.fire({
            title: 'Sending Notifications...',
            text: 'Please wait while we notify all users via email.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        try {
            const response = await fetch('{{ route("support.send-announcement") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message, type, metadata })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Save to localStorage history
                const history = JSON.parse(localStorage.getItem('announcement_history') || '[]');
                history.unshift({
                    id: Date.now(),
                    message: message,
                    type: type,
                    metadata: metadata,
                    createdAt: new Date().toISOString()
                });
                localStorage.setItem('announcement_history', JSON.stringify(history.slice(0, 20))); // Keep last 20
                
                Swal.fire({
                    icon: 'success',
                    title: 'Announcement Sent!',
                    html: `Email notifications have been dispatched to <strong>${data.sent_count} users</strong>.<br><small class="text-gray-500">${data.failed_count > 0 ? data.failed_count + ' failed' : ''}</small>`,
                    confirmButtonColor: '#3085d6'
                });
                loadHistory();
            } else {
                throw new Error(data.message || 'Failed to send');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to send announcement. Please check your connection and try again.',
                confirmButtonColor: '#d33'
            });
        }
    }
    
    // Form: Update Announcement
    const updateForm = document.getElementById('updateAnnouncementForm');
    if (updateForm) {
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = this.querySelector('textarea[name="message"]').value.trim();
            if (!message) {
                Swal.fire('Error', 'Please enter announcement message', 'error');
                return;
            }
            sendAnnouncement(message, 'general_update', {});
            this.querySelector('textarea[name="message"]').value = '';
        });
    }
    
    // Form: Shutdown
    const shutdownForm = document.getElementById('shutdownForm');
    if (shutdownForm) {
        shutdownForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = this.querySelector('textarea[name="message"]').value.trim();
            const startTime = this.querySelector('input[name="start_time"]').value;
            const endTime = this.querySelector('input[name="end_time"]').value;
            
            if (!message) {
                Swal.fire('Error', 'Please provide shutdown reason/message', 'error');
                return;
            }
            
            let fullMsg = `⚠️ SYSTEM MAINTENANCE / TEMPORARY SHUTDOWN:\n\n${message}`;
            if (startTime) {
                fullMsg += `\n\n📅 Start: ${new Date(startTime).toLocaleString()}`;
            }
            if (endTime) {
                fullMsg += `\n📅 End: ${new Date(endTime).toLocaleString()}`;
            }
            if (!startTime && !endTime) {
                fullMsg += `\n\n⚠️ Please be advised that the system may be temporarily unavailable.`;
            }
            
            sendAnnouncement(fullMsg, 'shutdown', { start_time: startTime, end_time: endTime });
            this.reset();
        });
    }
    
    // Quick create button
    const createBtn = document.getElementById('createAnnouncementBtn');
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Quick Broadcast',
                html: `
                    <textarea id="quickMsg" class="swal2-textarea" placeholder="Type your announcement here..." rows="4" style="resize: vertical;"></textarea>
                    <label class="flex items-center mt-3 cursor-pointer">
                        <input type="checkbox" id="isShutdownCheckbox" class="mr-2"> 
                        <span class="text-sm">⚠️ This is a temporary shutdown/maintenance alert</span>
                    </label>
                `,
                showCancelButton: true,
                confirmButtonText: 'Send Broadcast',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const msg = document.getElementById('quickMsg').value;
                    const isShutdown = document.getElementById('isShutdownCheckbox').checked;
                    if (!msg || msg.trim() === '') {
                        Swal.showValidationMessage('Please enter an announcement message');
                        return false;
                    }
                    return { msg: msg.trim(), isShutdown };
                }
            }).then((result) => {
                if (result.value) {
                    const type = result.value.isShutdown ? 'shutdown' : 'general_update';
                    sendAnnouncement(result.value.msg, type, {});
                }
            });
        });
    }
    
    // Clear history button
    const clearHistoryBtn = document.getElementById('clearHistoryBtn');
    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Clear History?',
                text: 'This will remove all announcement history from your browser. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('announcement_history');
                    loadHistory();
                    Swal.fire(
                        'Cleared!',
                        'Announcement history has been cleared.',
                        'success'
                    );
                }
            });
        });
    }
    
    // Load history on page load
    loadHistory();
});
</script>
@endpush
@endsection
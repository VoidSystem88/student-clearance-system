@extends('layouts.support')

@section('title', 'Maintenance Control')
@section('header', 'System Maintenance Control')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-5 border-b pb-3">
        <h3 class="text-xl font-bold text-gray-800">
            <i class="fas fa-tools text-orange-600 mr-2"></i> Maintenance Mode Control
        </h3>
        <div class="flex gap-2">
            <a href="{{ route('support.maintenance.logout') }}" class="text-sm text-gray-500 hover:text-red-600 transition">
                <i class="fas fa-sign-out-alt mr-1"></i> Lock Access
            </a>
            @if($isMaintenanceMode || $isReadOnlyMode)
                <button id="disableMaintenanceBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
                    <i class="fas fa-play-circle mr-1"></i> Disable Maintenance
                </button>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    @if($isMaintenanceMode || $isReadOnlyMode)
    <div class="mb-6 p-4 rounded-lg {{ $isMaintenanceMode ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
        <div class="flex items-center gap-3">
            <i class="fas {{ $isMaintenanceMode ? 'fa-skull-crosswalk text-red-600' : 'fa-eye-slash text-yellow-600' }} text-2xl"></i>
            <div class="flex-1">
                <h4 class="font-bold {{ $isMaintenanceMode ? 'text-red-800' : 'text-yellow-800' }}">
                    {{ $isMaintenanceMode ? '🔴 FULL SHUTDOWN ACTIVE' : '🟡 SOFT SHUTDOWN ACTIVE' }}
                </h4>
                <p class="text-sm">{{ $maintenanceMessage }}</p>
                @if($maintenanceEndTime)
                    <p class="text-xs mt-1">Expected end: {{ \Carbon\Carbon::parse($maintenanceEndTime)->format('F d, Y g:i A') }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Soft & Full Shutdown Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Soft Shutdown Card -->
        <div class="border border-gray-200 rounded-xl p-5 bg-gradient-to-br from-yellow-50 to-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye-slash text-yellow-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Soft Shutdown (Read-Only)</h4>
                    <p class="text-xs text-gray-500">Users can view but not submit</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">Students can view their clearance status but cannot submit new requests.</p>
            <form id="softShutdownForm" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maintenance Message</label>
                    <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm" placeholder="e.g., System is under maintenance..." required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Duration (hours)</label>
                    <select name="duration_hours" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        <option value="1">1 hour</option><option value="2">2 hours</option><option value="3">3 hours</option>
                        <option value="4">4 hours</option><option value="6">6 hours</option><option value="8">8 hours</option><option value="12">12 hours</option>
                    </select>
                </div>
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm w-full transition">
                    <i class="fas fa-eye-slash"></i> Enable Soft Shutdown
                </button>
            </form>
        </div>

        <!-- Full Shutdown Card -->
        @if(auth()->user()->role === 'support')
        <div class="border border-gray-200 rounded-xl p-5 bg-gradient-to-br from-red-50 to-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Full Shutdown</h4>
                    <p class="text-xs text-gray-500">⚠️ Support Only</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">Complete system lockdown. Use only for emergencies.</p>
            <form id="fullShutdownForm" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Shutdown Message</label>
                    <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm" placeholder="e.g., Emergency maintenance..." required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Duration (hours)</label>
                    <select name="duration_hours" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        <option value="1">1 hour</option><option value="2">2 hours</option><option value="3">3 hours</option>
                        <option value="4">4 hours</option><option value="6">6 hours</option>
                    </select>
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm w-full transition">
                    <i class="fas fa-exclamation-triangle"></i> Enable Full Shutdown
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Recent Maintenance Logs -->
    <div class="mt-8">
        <h4 class="font-semibold text-gray-700 mb-3"><i class="fas fa-history mr-2 text-gray-500"></i>Recent Maintenance Logs</h4>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($recentMaintenance as $log)
            <div class="border-l-4 {{ $log->mode === 'full' ? 'border-red-400 bg-red-50' : 'border-yellow-400 bg-yellow-50' }} p-3 rounded-r-lg shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="font-semibold {{ $log->mode === 'full' ? 'text-red-700' : 'text-yellow-700' }}">
                        {{ $log->mode === 'full' ? 'FULL SHUTDOWN' : 'SOFT SHUTDOWN' }}
                        @if($log->is_active)<span class="ml-2 text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">ACTIVE</span>@endif
                    </span>
                    <span class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <p class="text-sm mt-1">{{ Str::limit($log->message, 100) }}</p>
                <p class="text-xs text-gray-500 mt-1">Initiated by: {{ $log->initiatedBy->name ?? 'Unknown' }}
                    @if($log->end_time) | Expected end: {{ \Carbon\Carbon::parse($log->end_time)->format('M d, g:i A') }} @endif
                </p>
            </div>
            @empty
            <div class="text-center text-gray-400 text-sm py-4">No maintenance logs found</div>
            @endforelse
        </div>
    </div>

    <!-- ✅ DOWNLOAD BACKUP SECTION (NASA LOOB NG MAIN CARD) -->
    <div class="mt-8 border-t pt-6">
        <h4 class="font-semibold text-gray-700 mb-3"><i class="fas fa-download mr-2 text-blue-500"></i>Download System Backup</h4>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-archive text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h5 class="font-semibold text-gray-800">Full System Backup</h5>
                    <p class="text-sm text-gray-600 mt-1">Download the entire system as a ZIP archive.</p>
                    <div class="mt-3">
                        <span class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Includes: app, config, database, public, resources, routes, .env</span>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.download.backup') }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm"
                           onclick="return confirmDownload()">
                            <i class="fas fa-download"></i> Download Full Backup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ✅ END OF MAIN CARD --}}
@endsection

@push('scripts')
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('softShutdownForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const message = this.querySelector('textarea[name="message"]').value.trim();
    const duration = this.querySelector('select[name="duration_hours"]').value;
    if (!message) { Swal.fire('Error', 'Please enter maintenance message', 'error'); return; }
    Swal.fire({ title: 'Enable Soft Shutdown?', text: `System will become read-only for ${duration} hour(s).`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, enable' }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Enabling...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const response = await fetch('{{ route("support.maintenance.soft") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ message, duration_hours: parseInt(duration) }) });
            const data = await response.json();
            if (data.success) Swal.fire('Success', data.message, 'success').then(() => location.reload());
            else Swal.fire('Error', data.error || 'Failed', 'error');
        }
    });
});

const fullShutdownForm = document.getElementById('fullShutdownForm');
if (fullShutdownForm) {
    fullShutdownForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = this.querySelector('textarea[name="message"]').value.trim();
        const duration = this.querySelector('select[name="duration_hours"]').value;
        if (!message) { Swal.fire('Error', 'Please enter shutdown message', 'error'); return; }
        Swal.fire({ title: '⚠️ ENABLE FULL SHUTDOWN ⚠️', text: `System offline for ${duration} hour(s).`, icon: 'error', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, shutdown' }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Shutting down...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const response = await fetch('{{ route("support.maintenance.full") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ message, duration_hours: parseInt(duration) }) });
                const data = await response.json();
                if (data.success) Swal.fire('System Shutdown', data.message, 'warning').then(() => location.reload());
                else Swal.fire('Error', data.error || 'Failed', 'error');
            }
        });
    });
}

const disableBtn = document.getElementById('disableMaintenanceBtn');
if (disableBtn) {
    disableBtn.addEventListener('click', async function() {
        Swal.fire({ title: 'Disable Maintenance Mode?', text: 'System will return to normal.', icon: 'question', showCancelButton: true, confirmButtonColor: '#3085d6', confirmButtonText: 'Yes' }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Disabling...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const response = await fetch('{{ route("support.maintenance.disable") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token } });
                const data = await response.json();
                if (data.success) Swal.fire('Success', data.message, 'success').then(() => location.reload());
                else Swal.fire('Error', data.error || 'Failed', 'error');
            }
        });
    });
}

function confirmDownload() {
    return Swal.fire({
        title: 'Download System Backup?',
        text: 'This will download the entire project as a ZIP file.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, download now'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Preparing Backup...', text: 'Your download will start shortly.', icon: 'info', timer: 2000, showConfirmButton: false });
            return true;
        }
        return false;
    });
}
</script>
@endpush
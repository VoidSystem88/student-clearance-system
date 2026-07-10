@extends('layouts.support')

@section('title', 'Support Requests')
@section('header', 'Support Requests')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-ticket-alt text-blue-600 mr-2"></i> Student Support Requests
            </h3>
            <p class="text-sm text-gray-500">View and manage student assistance requests with attachments</p>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchRequest" placeholder="Search by student name, ID, or request type..." 
                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="requestsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Issue Type</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Description</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Attachment</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="requestsTableBody">
                @forelse($supportRequests ?? [] as $request)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm font-mono text-gray-600">#{{ $request->id }}</td>
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-800">{{ $request->student->first_name ?? '' }} {{ $request->student->last_name ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ $request->student->student_id ?? '' }}</div>
                        <div class="text-xs text-gray-400">{{ $request->student->email ?? '' }}</div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            @if($request->request_type == 'password_reset') bg-blue-100 text-blue-700
                            @elseif($request->request_type == 'account_id_reset') bg-purple-100 text-purple-700
                            @elseif($request->request_type == 'login_issue') bg-red-100 text-red-700
                            @elseif($request->request_type == 'otp_issue') bg-yellow-100 text-yellow-700
                            @elseif($request->request_type == 'clearance_issue') bg-orange-100 text-orange-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ str_replace('_', ' ', ucfirst($request->request_type)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600 max-w-xs">
                        <p class="line-clamp-2">{{ Str::limit($request->description, 80) }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @if($request->attachment_path)
                            @php
                                $filename = basename($request->attachment_path);
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                $fileUrl = url('/file/' . $filename);
                            @endphp
                            
                            @if($isImage)
                                <button onclick="openImageViewer('{{ $fileUrl }}', '{{ addslashes($request->student->first_name . ' ' . $request->student->last_name) }}', '{{ $filename }}')" 
                                        class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                    <i class="fas fa-image"></i> View Image
                                </button>
                                <a href="{{ url('/file/' . $filename . '?download=1') }}" 
                                   class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 text-sm transition ml-2">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <a href="{{ url('/file/' . $filename . '?download=1') }}" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">No attachment</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            @if($request->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($request->status == 'in_progress') bg-blue-100 text-blue-700
                            @elseif($request->status == 'resolved') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-700 @endif">
                            <i class="fas 
                                @if($request->status == 'pending') fa-clock
                                @elseif($request->status == 'in_progress') fa-spinner fa-pulse
                                @elseif($request->status == 'resolved') fa-check-circle
                                @else fa-times-circle @endif text-xs"></i>
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500">
                        {{ $request->created_at->format('M d, Y') }}
                        <br><span class="text-xs">{{ $request->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <button onclick="openRequestModal({{ $request->id }})" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-eye"></i> View & Respond
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No support requests yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- View & Respond Modal -->
<div id="requestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100 bg-gray-50 sticky top-0">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-ticket-alt text-blue-600 mr-2"></i> Request Details
            </h3>
            <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-5">
            <div id="requestDetails" class="mb-4">
                <!-- Dynamic content will be loaded here -->
            </div>
            
            <form id="updateRequestForm" method="POST">
                @csrf
                <input type="hidden" name="request_id" id="requestId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 text-sm font-medium">Update Status</label>
                    <select name="status" id="statusSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="pending">⏳ Pending</option>
                        <option value="in_progress">🔄 In Progress</option>
                        <option value="resolved">✅ Resolved</option>
                        <option value="cancelled">❌ Cancelled</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 text-sm font-medium">Response / Admin Notes</label>
                    <textarea name="admin_notes" id="adminNotes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Add your response or notes here..."></textarea>
                </div>
                
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeRequestModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> Update & Respond
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4" style="display: none;">
    <div class="relative max-w-4xl w-full max-h-full">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
            <div class="flex justify-between items-center px-5 py-3 bg-gray-100 border-b border-gray-200">
                <h3 id="imageViewerTitle" class="font-semibold text-gray-800">
                    <i class="fas fa-image text-blue-600 mr-2"></i> Student Attachment
                </h3>
                <button onclick="closeImageViewer()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-4 bg-gray-50 flex items-center justify-center" style="min-height: 300px; max-height: 70vh; overflow-y: auto;">
                <div id="imageLoadingSpinner" class="flex flex-col items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                    <p class="text-gray-500 text-sm">Loading image...</p>
                </div>
                <img id="imageViewerSrc" src="" alt="Student Attachment" class="max-w-full max-h-full object-contain rounded-lg hidden">
                <div id="imageErrorMsg" class="hidden text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-3"></i>
                    <p class="text-red-600 font-medium">Failed to load image</p>
                    <p id="imageErrorDetail" class="text-gray-500 text-sm mt-2"></p>
                    <button onclick="retryLoadImage()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-sync-alt mr-1"></i> Retry
                    </button>
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                <a id="downloadImageBtn" href="#" download class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-download"></i> Download
                </a>
                <button onclick="closeImageViewer()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentImageUrlForRetry = '';
    let currentStudentNameForRetry = '';
    let currentFilenameForRetry = '';
    
    // ============ SEARCH FUNCTIONALITY ============
    document.getElementById('searchRequest')?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#requestsTableBody tr');
        let hasResults = false;
        
        rows.forEach(row => {
            // Skip if it's a no-result row
            if (row.id === 'noResultRow') return;
            
            let text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        let noResultRow = document.getElementById('noResultRow');
        if (!hasResults && rows.length > 0 && rows[0].cells) {
            if (!noResultRow) {
                let tbody = document.getElementById('requestsTableBody');
                let tr = document.createElement('tr');
                tr.id = 'noResultRow';
                tr.innerHTML = `<td colspan="8" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                    <p>No requests found for "<span id="searchTermDisplay"></span>"</p>
                                  </td>`;
                tbody.appendChild(tr);
                noResultRow = tr;
            }
            document.getElementById('searchTermDisplay').innerText = searchTerm;
            noResultRow.style.display = '';
        } else if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    });
    
    // ============ VIEW & RESPOND MODAL ============
    function openRequestModal(id) {
        fetch('/support/request/' + id + '/json')
            .then(response => response.json())
            .then(data => {
                let detailsHtml = `
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Request ID:</span>
                                <span class="font-medium text-gray-800 ml-2">#${data.id}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ml-2
                                    ${data.status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                      data.status == 'in_progress' ? 'bg-blue-100 text-blue-700' : 
                                      data.status == 'resolved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}">
                                    ${data.status}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Student:</span>
                                <span class="text-gray-800 ml-2">${data.student_name}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Student ID:</span>
                                <span class="text-gray-800 ml-2">${data.student_id}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Request Type:</span>
                                <span class="text-gray-800 ml-2">${data.request_type.replace(/_/g, ' ')}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Submitted:</span>
                                <span class="text-gray-800 ml-2">${data.created_at}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Description:</label>
                        <div class="bg-gray-50 rounded-lg p-3 text-gray-700 text-sm">${escapeHtml(data.description)}</div>
                    </div>
                `;
                
                // Add attachment section if exists
                if (data.attachment_path) {
                    const filename = data.attachment_original_name || 'attachment';
                    const extension = filename.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(extension);
                    
                    if (isImage) {
                        detailsHtml += `
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-1">Attachment:</label>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <img src="${data.attachment_path}" 
                                         alt="Attachment" 
                                         class="max-w-full max-h-48 rounded-lg border cursor-pointer mb-2"
                                         onclick="openImageViewer('${data.attachment_path}', '${data.student_name}', '${filename}')"
                                         onerror="this.src='https://placehold.co/400x200?text=Image+Not+Found'">
                                    <div class="flex gap-2">
                                        <button onclick="openImageViewer('${data.attachment_path}', '${data.student_name}', '${filename}')" 
                                                class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-expand"></i> View Full Size
                                        </button>
                                        <a href="${data.attachment_path}?download=1" 
                                           class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 text-sm">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        detailsHtml += `
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-1">Attachment:</label>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <a href="${data.attachment_path}?download=1" 
                                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-file-download"></i> Download ${filename}
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
                
                if (data.admin_notes) {
                    detailsHtml += `
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-1">Previous Response:</label>
                            <div class="bg-blue-50 rounded-lg p-3 text-gray-700 text-sm border-l-4 border-blue-500">${escapeHtml(data.admin_notes)}</div>
                        </div>
                    `;
                }
                
                document.getElementById('requestDetails').innerHTML = detailsHtml;
                document.getElementById('requestId').value = data.id;
                document.getElementById('statusSelect').value = data.status;
                document.getElementById('adminNotes').value = data.admin_notes || '';
                document.getElementById('updateRequestForm').action = '/support/request/' + id + '/status';
                
                document.getElementById('requestModal').classList.remove('hidden');
                document.getElementById('requestModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load request details');
            });
    }
    
    function closeRequestModal() {
        document.getElementById('requestModal').classList.add('hidden');
        document.getElementById('requestModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // ============ IMAGE VIEWER FUNCTIONS ============
    function openImageViewer(imageUrl, studentName, filename) {
        currentImageUrlForRetry = imageUrl;
        currentStudentNameForRetry = studentName;
        currentFilenameForRetry = filename || '';
        
        const modal = document.getElementById('imageViewerModal');
        const title = document.getElementById('imageViewerTitle');
        const downloadBtn = document.getElementById('downloadImageBtn');
        const spinner = document.getElementById('imageLoadingSpinner');
        const img = document.getElementById('imageViewerSrc');
        const errorMsg = document.getElementById('imageErrorMsg');
        
        spinner.classList.remove('hidden');
        img.classList.add('hidden');
        errorMsg.classList.add('hidden');
        img.src = '';
        
        title.innerHTML = '<i class="fas fa-image text-blue-600 mr-2"></i> ' + studentName + '\'s Attachment';
        downloadBtn.href = imageUrl + '?download=1';
        
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Load image
        const testImg = new Image();
        testImg.onload = function() {
            img.src = imageUrl;
            spinner.classList.add('hidden');
            img.classList.remove('hidden');
            errorMsg.classList.add('hidden');
        };
        testImg.onerror = function() {
            spinner.classList.add('hidden');
            errorMsg.classList.remove('hidden');
            document.getElementById('imageErrorDetail').innerHTML = 'Failed to load: ' + imageUrl;
        };
        testImg.src = imageUrl;
    }
    
    function retryLoadImage() {
        openImageViewer(currentImageUrlForRetry, currentStudentNameForRetry, currentFilenameForRetry);
    }
    
    function closeImageViewer() {
        const modal = document.getElementById('imageViewerModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Helper functions
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    // Close modals when clicking outside
    document.getElementById('requestModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRequestModal();
    });
    
    document.getElementById('imageViewerModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeImageViewer();
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRequestModal();
            closeImageViewer();
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 0.8s linear infinite;
    }
</style>
@endsection
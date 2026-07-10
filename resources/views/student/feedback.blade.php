@extends('layouts.app')

@section('title', 'Feedback')
@section('header', 'Feedback & Suggestions')

@push('styles')
<style>
    /* ✅ FEEDBACK ACTION BUTTONS - ALWAYS VISIBLE, MOBILE FRIENDLY */
    .feedback-actions {
        display: flex;
        gap: 8px;
        opacity: 1 !important;
        visibility: visible !important;
        margin-top: 8px;
    }

    .feedback-edit-btn,
    .feedback-delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        min-width: 44px;
        min-height: 44px;
        flex: 1;
    }

    .feedback-edit-btn {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .feedback-edit-btn:hover, .feedback-edit-btn:active {
        background: #2563eb;
        color: white;
    }

    .feedback-delete-btn {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .feedback-delete-btn:hover, .feedback-delete-btn:active {
        background: #dc2626;
        color: white;
    }

    body.dark .feedback-edit-btn {
        background: #1e3a5f;
        color: #60a5fa;
        border-color: #1e40af;
    }
    body.dark .feedback-edit-btn:hover {
        background: #2563eb;
        color: white;
    }

    body.dark .feedback-delete-btn {
        background: #7f1d1d;
        color: #fca5a5;
        border-color: #991b1b;
    }
    body.dark .feedback-delete-btn:hover {
        background: #dc2626;
        color: white;
    }

    @media (max-width: 640px) {
        .feedback-edit-btn,
        .feedback-delete-btn {
            padding: 12px 18px;
            font-size: 14px;
            min-width: 48px;
            min-height: 48px;
            border-radius: 12px;
        }
        .feedback-item {
            padding: 16px 10px !important;
        }
        .feedback-actions {
            gap: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Feedback Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/30">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-comment-dots text-purple-600 dark:text-purple-400 mr-2"></i> <span id="formTitle">Share Your Feedback</span>
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" id="formSubtitle">Help us improve the system by sharing your thoughts</p>
        </div>
        
        <div class="p-5">
            <div id="alertMessage" class="hidden rounded-lg p-3 mb-4"></div>
            
            <form id="feedbackForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                
                <!-- Rating Stars -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Rating *</label>
                    <div class="flex gap-2 rating-stars">
                        <button type="button" data-rating="1" class="star-btn text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="2" class="star-btn text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="3" class="star-btn text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="4" class="star-btn text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="5" class="star-btn text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition">★</button>
                    </div>
                    <input type="hidden" name="rating" id="rating_value" value="{{ old('rating') }}">
                    <p id="rating_error" class="text-red-500 text-xs mt-1 hidden">Please select a rating</p>
                </div>
                
                <!-- Category Dropdown -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Category *</label>
                    <div class="relative">
                        <button type="button" id="categoryButton" 
                                class="w-full px-3 py-3 text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-between">
                            <span id="selectedCategory" class="flex items-center gap-2">
                                <i class="fas fa-folder-open text-gray-500 dark:text-gray-400"></i>
                                <span id="selectedCategoryText">Select Category</span>
                            </span>
                            <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400"></i>
                        </button>
                        
                        <div id="categoryDropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="dropdown-category px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="bug">
                                <i class="fas fa-bug text-red-500 w-5"></i>
                                <span>Bug Report</span>
                            </div>
                            <div class="dropdown-category px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="feature">
                                <i class="fas fa-lightbulb text-yellow-500 w-5"></i>
                                <span>Feature Request</span>
                            </div>
                            <div class="dropdown-category px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="improvement">
                                <i class="fas fa-chart-line text-green-500 w-5"></i>
                                <span>Improvement Suggestion</span>
                            </div>
                            <div class="dropdown-category px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="experience">
                                <i class="fas fa-smile-wink text-blue-500 w-5"></i>
                                <span>User Experience</span>
                            </div>
                            <div class="dropdown-category px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="other">
                                <i class="fas fa-comment-dots text-purple-500 w-5"></i>
                                <span>Other</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="category_value" value="{{ old('category') }}">
                    <p id="category_error" class="text-red-500 text-xs mt-1 hidden">Please select a category</p>
                </div>
                
                <!-- Message -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Your Message *</label>
                    <textarea name="message" id="message" rows="5" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Please share your feedback, suggestions, or report any issues..." required>{{ old('message') }}</textarea>
                    <p id="message_error" class="text-red-500 text-xs mt-1 hidden">Message must be at least 10 characters</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Minimum 10 characters
                    </p>
                </div>
                
                <div class="flex justify-end mt-6 gap-2">
                    <button type="button" id="cancelEditBtn" 
                        class="hidden px-5 py-3 rounded-lg text-sm font-medium transition bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    <button type="submit" id="feedbackSubmitBtn" 
                        class="px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i> 
                        <span id="feedbackBtnText">Submit Feedback</span>
                        <span id="feedbackBtnSpinner" class="hidden"><i class="fas fa-spinner fa-spin ml-1"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Recent Feedbacks -->
    <div id="feedbacksContainer" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/30 rounded-t-xl">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-history text-purple-600 dark:text-purple-400 mr-2"></i> Your Feedback History
            </h3>
        </div>
        <div id="feedbacksList" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
            @forelse($feedbacks as $fb)
            <div class="feedback-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="{{ $fb->id }}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fb->rating)
                                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                                @else
                                    <i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex items-center gap-1
                            @if($fb->category == 'bug') bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300
                            @elseif($fb->category == 'feature') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300
                            @elseif($fb->category == 'improvement') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300
                            @elseif($fb->category == 'experience') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300
                            @else bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 @endif">
                            @if($fb->category == 'bug')<i class="fas fa-bug text-xs"></i>
                            @elseif($fb->category == 'feature')<i class="fas fa-lightbulb text-xs"></i>
                            @elseif($fb->category == 'improvement')<i class="fas fa-chart-line text-xs"></i>
                            @elseif($fb->category == 'experience')<i class="fas fa-smile-wink text-xs"></i>
                            @else<i class="fas fa-comment-dots text-xs"></i>@endif
                            {{ ucfirst($fb->category) }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->created_at->format('M d, Y') }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 feedback-message">{{ $fb->message }}</p>
                
                @if($fb->admin_response)
                    <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-600 dark:text-blue-400">
                            <i class="fas fa-headset mr-1"></i> <strong>Admin Response:</strong> {{ $fb->admin_response }}
                        </p>
                    </div>
                @endif
                
                <!-- ✅ ALWAYS VISIBLE EDIT/DELETE BUTTONS -->
                <div class="feedback-actions">
                    <button onclick="editFeedback({{ $fb->id }}, '{{ addslashes($fb->message) }}', {{ $fb->rating }}, '{{ $fb->category }}')" 
                        class="feedback-edit-btn">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button onclick="deleteFeedback({{ $fb->id }})" 
                        class="feedback-delete-btn">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400" id="emptyFeedbacks">
                <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                <p>No feedback yet</p>
                <p class="text-xs mt-1">Share your thoughts using the form</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating_value');
    let currentRating = {{ old('rating', 0) }};
    let currentCategory = '{{ old('category', '') }}';
    let editingId = null;
    
    function setStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-300', 'dark:text-gray-600');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300', 'dark:text-gray-600');
            }
        });
        ratingInput.value = rating;
        currentRating = rating;
    }
    
    if (currentRating > 0) setStars(currentRating);
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            setStars(parseInt(this.dataset.rating));
            document.getElementById('rating_error').classList.add('hidden');
        });
    });
    
    const categoryButton = document.getElementById('categoryButton');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const selectedCategoryText = document.getElementById('selectedCategoryText');
    const categoryInput = document.getElementById('category_value');
    
    function setCategory(value) {
        const option = document.querySelector(`.dropdown-category[data-value="${value}"]`);
        if (option) {
            const icon = option.querySelector('i').cloneNode(true);
            const text = option.querySelector('span').textContent;
            document.getElementById('selectedCategory').innerHTML = '';
            document.getElementById('selectedCategory').appendChild(icon);
            document.getElementById('selectedCategory').appendChild(document.createTextNode(' ' + text));
            selectedCategoryText.textContent = text;
            categoryInput.value = value;
            currentCategory = value;
            document.getElementById('category_error').classList.add('hidden');
        }
    }
    
    if (currentCategory) setCategory(currentCategory);
    
    if (categoryButton) {
        categoryButton.addEventListener('click', function(e) {
            e.stopPropagation();
            categoryDropdown.classList.toggle('hidden');
        });
    }
    
    document.querySelectorAll('.dropdown-category').forEach(item => {
        item.addEventListener('click', function() {
            setCategory(this.getAttribute('data-value'));
            categoryDropdown.classList.add('hidden');
        });
    });
    
    document.addEventListener('click', function(e) {
        if (categoryButton && categoryDropdown && !categoryButton.contains(e.target) && !categoryDropdown.contains(e.target)) {
            categoryDropdown.classList.add('hidden');
        }
    });
    
    function editFeedback(id, message, rating, category) {
        editingId = id;
        document.getElementById('formTitle').textContent = 'Edit Feedback';
        document.getElementById('formSubtitle').textContent = 'Update your feedback';
        document.getElementById('edit_id').value = id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('message').value = message;
        document.getElementById('feedbackBtnText').textContent = 'Update Feedback';
        document.getElementById('cancelEditBtn').classList.remove('hidden');
        setStars(rating);
        setCategory(category);
        document.getElementById('feedbackForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        document.getElementById('feedbackForm').closest('.bg-white, .dark\\:bg-gray-800').classList.add('ring-2', 'ring-purple-500');
        setTimeout(() => {
            document.getElementById('feedbackForm').closest('.bg-white, .dark\\:bg-gray-800').classList.remove('ring-2', 'ring-purple-500');
        }, 2000);
    }
    
    function cancelEdit() {
        editingId = null;
        document.getElementById('formTitle').textContent = 'Share Your Feedback';
        document.getElementById('formSubtitle').textContent = 'Help us improve the system by sharing your thoughts';
        document.getElementById('edit_id').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('message').value = '';
        document.getElementById('feedbackBtnText').textContent = 'Submit Feedback';
        document.getElementById('cancelEditBtn').classList.add('hidden');
        setStars(0);
        setCategory('');
        currentCategory = '';
        document.getElementById('selectedCategory').innerHTML = '<i class="fas fa-folder-open text-gray-500 dark:text-gray-400"></i><span id="selectedCategoryText">Select Category</span>';
        selectedCategoryText.textContent = 'Select Category';
        categoryInput.value = '';
    }
    
    document.getElementById('cancelEditBtn').addEventListener('click', cancelEdit);
    
    async function deleteFeedback(id) {
        const result = await Swal.fire({
            title: 'Delete Feedback?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });
        
        if (result.isConfirmed) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch(`/student/feedback/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const item = document.querySelector(`.feedback-item[data-id="${id}"]`);
                    if (item) item.remove();
                    if (document.querySelectorAll('.feedback-item').length === 0) {
                        document.getElementById('feedbacksList').innerHTML = `
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                <p>No feedback yet</p>
                                <p class="text-xs mt-1">Share your thoughts using the form</p>
                            </div>`;
                    }
                    if (editingId === id) cancelEdit();
                    showAlert('Feedback deleted successfully!', 'success');
                } else {
                    showAlert(data.message || 'Failed to delete', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Network error. Please try again.', 'error');
            }
        }
    }
    
    function showAlert(message, type) {
        const alertMessage = document.getElementById('alertMessage');
        alertMessage.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;
        alertMessage.className = `rounded-lg p-3 mb-4 ${type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-l-4 border-green-500' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-l-4 border-red-500'}`;
        alertMessage.classList.remove('hidden');
        setTimeout(() => alertMessage.classList.add('hidden'), 5000);
    }
    
    function validateForm() {
        let isValid = true;
        if (!currentRating || currentRating === 0) { document.getElementById('rating_error').classList.remove('hidden'); isValid = false; }
        else { document.getElementById('rating_error').classList.add('hidden'); }
        if (!currentCategory) { document.getElementById('category_error').classList.remove('hidden'); isValid = false; }
        else { document.getElementById('category_error').classList.add('hidden'); }
        const message = document.getElementById('message').value.trim();
        if (message.length < 10) { document.getElementById('message_error').classList.remove('hidden'); isValid = false; }
        else { document.getElementById('message_error').classList.add('hidden'); }
        return isValid;
    }
    
    const form = document.getElementById('feedbackForm');
    const feedbackBtn = document.getElementById('feedbackSubmitBtn');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) { showAlert('Please fix the errors above.', 'error'); return; }
        if (feedbackBtn.disabled) return;
        
        feedbackBtn.disabled = true;
        document.getElementById('feedbackBtnText').textContent = editingId ? 'Updating...' : 'Submitting...';
        document.getElementById('feedbackBtnSpinner').classList.remove('hidden');
        
        const formData = new FormData(form);
        const url = editingId ? `/student/feedback/${editingId}` : '/student/feedback';
        if (editingId) formData.append('_method', 'PUT');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            showAlert('Session expired. Please refresh the page.', 'error');
            setTimeout(() => location.reload(), 2000);
            feedbackBtn.disabled = false;
            document.getElementById('feedbackBtnText').textContent = editingId ? 'Update Feedback' : 'Submit Feedback';
            document.getElementById('feedbackBtnSpinner').classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: formData
            });
            
            if (response.redirected) {
                showAlert('Session expired. Refreshing page...', 'error');
                setTimeout(() => location.reload(), 1500);
                return;
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                showAlert('Your session has expired. Please refresh the page.', 'error');
                setTimeout(() => location.reload(), 2000);
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                if (editingId) {
                    updateFeedbackInList(editingId, data.feedback);
                    cancelEdit();
                    showAlert('Feedback updated successfully!', 'success');
                } else {
                    setStars(0); setCategory(''); currentCategory = '';
                    document.getElementById('message').value = '';
                    document.getElementById('selectedCategory').innerHTML = '<i class="fas fa-folder-open text-gray-500 dark:text-gray-400"></i><span id="selectedCategoryText">Select Category</span>';
                    selectedCategoryText.textContent = 'Select Category'; categoryInput.value = '';
                    showAlert(data.message || 'Feedback submitted!', 'success');
                    if (data.feedback) addFeedbackToList(data.feedback);
                }
            } else {
                showAlert(data.message || 'Something went wrong', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Connection error. Please try again.', 'error');
        } finally {
            feedbackBtn.disabled = false;
            document.getElementById('feedbackBtnText').textContent = editingId ? 'Update Feedback' : 'Submit Feedback';
            document.getElementById('feedbackBtnSpinner').classList.add('hidden');
        }
    });
    
    function updateFeedbackInList(id, feedback) {
        const item = document.querySelector(`.feedback-item[data-id="${id}"]`);
        if (!item) return;
        const starsContainer = item.querySelector('.flex.items-center.gap-2 .flex');
        if (starsContainer) {
            starsContainer.innerHTML = Array(5).fill().map((_, i) => 
                i < feedback.rating ? '<i class="fas fa-star text-yellow-500 text-sm"></i>' : '<i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>'
            ).join('');
        }
        const messageEl = item.querySelector('.feedback-message');
        if (messageEl) messageEl.textContent = feedback.message;
    }
    
    function addFeedbackToList(feedback) {
        const container = document.getElementById('feedbacksList');
        const emptyDiv = document.getElementById('emptyFeedbacks');
        if (emptyDiv) emptyDiv.remove();
        
        const date = new Date();
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        const categoryIcons = { 'bug': 'fa-bug', 'feature': 'fa-lightbulb', 'improvement': 'fa-chart-line', 'experience': 'fa-smile-wink', 'other': 'fa-comment-dots' };
        const categoryColors = {
            'bug': 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            'feature': 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
            'improvement': 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
            'experience': 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
            'other': 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300'
        };
        
        const starsHtml = Array(5).fill().map((_, i) => 
            i < feedback.rating ? '<i class="fas fa-star text-yellow-500 text-sm"></i>' : '<i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>'
        ).join('');
        
        const newHtml = `
            <div class="feedback-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="${feedback.id}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex">${starsHtml}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex items-center gap-1 ${categoryColors[feedback.category]}">
                            <i class="fas ${categoryIcons[feedback.category]} text-xs"></i>
                            ${feedback.category.charAt(0).toUpperCase() + feedback.category.slice(1)}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">${formattedDate}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 feedback-message">${escapeHtml(feedback.message)}</p>
                <div class="feedback-actions">
                    <button onclick="editFeedback(${feedback.id}, '${escapeHtml(feedback.message)}', ${feedback.rating}, '${feedback.category}')" class="feedback-edit-btn">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button onclick="deleteFeedback(${feedback.id})" class="feedback-delete-btn">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>`;
        
        container.insertAdjacentHTML('afterbegin', newHtml);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection
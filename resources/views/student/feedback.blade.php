@extends('layouts.app')

@section('title', 'Feedback')
@section('header', 'Feedback & Suggestions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Feedback Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/30">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                <i class="fas fa-comment-dots text-purple-600 dark:text-purple-400 mr-2"></i> Share Your Feedback
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Help us improve the system by sharing your thoughts</p>
        </div>
        
        <div class="p-5">
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('student.feedback.store') }}">
                @csrf
                
                <!-- Rating Stars -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Rating *</label>
                    <div class="flex gap-2 rating-stars">
                        <button type="button" data-rating="1" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn">★</button>
                        <button type="button" data-rating="2" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn">★</button>
                        <button type="button" data-rating="3" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn">★</button>
                        <button type="button" data-rating="4" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn">★</button>
                        <button type="button" data-rating="5" class="text-3xl text-gray-300 hover:text-yellow-400 transition star-btn">★</button>
                    </div>
                    <input type="hidden" name="rating" id="rating_value" required>
                </div>
                
                <!-- Category with Custom Dropdown Icons -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Category *</label>
                    <div class="relative">
                        <button type="button" id="categoryButton" 
                                class="w-full px-3 py-2 text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-between">
                            <span id="selectedCategory" class="flex items-center gap-2">
                                <i class="fas fa-folder-open text-gray-500"></i>
                                <span> Select Category </span>
                            </span>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </button>
                        
                        <div id="categoryDropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="bug">
                                <i class="fas fa-bug text-red-500 w-5"></i>
                                <span>Bug Report</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="feature">
                                <i class="fas fa-lightbulb text-yellow-500 w-5"></i>
                                <span>Feature Request</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="improvement">
                                <i class="fas fa-chart-line text-green-500 w-5"></i>
                                <span>Improvement Suggestion</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="experience">
                                <i class="fas fa-smile-wink text-blue-500 w-5"></i>
                                <span>User Experience</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="other">
                                <i class="fas fa-comment-dots text-purple-500 w-5"></i>
                                <span>Other</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="category_value" required>
                </div>
                
                <!-- Message -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Your Message *</label>
                    <textarea name="message" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Please share your feedback, suggestions, or report any issues..." required></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Minimum 10 characters
                    </p>
                </div>
                
                <div class="flex justify-end mt-6 gap-2">
                    
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-purple-600 text-white hover:bg-purple-700 dark:bg-purple-600 dark:text-white dark:hover:bg-purple-700">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Recent Feedbacks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                <i class="fas fa-history text-purple-600 dark:text-purple-400 mr-2"></i> Your Previous Feedback
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
            @forelse($feedbacks as $fb)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
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
                            @if($fb->category == 'bug')
                                <i class="fas fa-bug text-xs"></i>
                            @elseif($fb->category == 'feature')
                                <i class="fas fa-lightbulb text-xs"></i>
                            @elseif($fb->category == 'improvement')
                                <i class="fas fa-chart-line text-xs"></i>
                            @elseif($fb->category == 'experience')
                                <i class="fas fa-smile-wink text-xs"></i>
                            @else
                                <i class="fas fa-comment-dots text-xs"></i>
                            @endif
                            {{ ucfirst($fb->category) }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $fb->created_at->format('M d, Y') }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $fb->message }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                        @if($fb->status == 'pending') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300
                        @elseif($fb->status == 'reviewed') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300
                        @else bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 @endif">
                        <i class="fas 
                            @if($fb->status == 'pending') fa-clock
                            @elseif($fb->status == 'reviewed') fa-eye
                            @else fa-check @endif text-xs"></i>
                        {{ ucfirst($fb->status) }}
                    </span>
                    @if($fb->admin_response)
                        <span class="text-xs text-blue-600 dark:text-blue-400">
                            <i class="fas fa-reply mr-1"></i> Admin responded
                        </span>
                    @endif
                </div>
                @if($fb->admin_response)
                    <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-600 dark:text-blue-400">
                            <i class="fas fa-headset mr-1"></i> <strong>Admin Response:</strong> {{ $fb->admin_response }}
                        </p>
                    </div>
                @endif
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                <p>No feedback yet</p>
                <p class="text-xs mt-1">Share your thoughts using the form</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    // Star rating functionality
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating_value');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
    });
    
    // ============ CUSTOM CATEGORY DROPDOWN ============
    const categoryButton = document.getElementById('categoryButton');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const selectedCategory = document.getElementById('selectedCategory');
    const categoryInput = document.getElementById('category_value');
    
    if (categoryButton) {
        categoryButton.addEventListener('click', function(e) {
            e.stopPropagation();
            categoryDropdown.classList.toggle('hidden');
        });
    }
    
    document.querySelectorAll('.dropdown-category').forEach(item => {
        item.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const icon = this.querySelector('i').cloneNode(true);
            const text = this.querySelector('span').textContent;
            
            selectedCategory.innerHTML = '';
            selectedCategory.appendChild(icon);
            selectedCategory.appendChild(document.createTextNode(' ' + text));
            
            categoryInput.value = value;
            categoryDropdown.classList.add('hidden');
            
            document.querySelectorAll('.dropdown-category').forEach(i => {
                i.classList.remove('bg-purple-50', 'dark:bg-purple-900/30');
            });
            this.classList.add('bg-purple-50', 'dark:bg-purple-900/30');
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (categoryButton && categoryDropdown) {
            if (!categoryButton.contains(e.target) && !categoryDropdown.contains(e.target)) {
                categoryDropdown.classList.add('hidden');
            }
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
    
    #categoryDropdown {
        transition: all 0.2s ease;
    }
    
    .dropdown-category {
        transition: all 0.2s ease;
    }
</style>
@endsection
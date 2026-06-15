<?php $__env->startSection('title', 'Feedback'); ?>
<?php $__env->startSection('header', 'Feedback & Suggestions'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Feedback Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/30">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-comment-dots text-purple-600 dark:text-purple-400 mr-2"></i> Share Your Feedback
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Help us improve the system by sharing your thoughts</p>
        </div>
        
        <div class="p-5">
            <div id="alertMessage" class="hidden rounded-lg p-3 mb-4"></div>
            
            <form id="feedbackForm" method="POST">
                <?php echo csrf_field(); ?>
                
                <!-- Rating Stars -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Rating *</label>
                    <div class="flex gap-2 rating-stars">
                        <button type="button" data-rating="1" class="star-btn text-3xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 dark:hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="2" class="star-btn text-3xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 dark:hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="3" class="star-btn text-3xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 dark:hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="4" class="star-btn text-3xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 dark:hover:text-yellow-400 transition">★</button>
                        <button type="button" data-rating="5" class="star-btn text-3xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 dark:hover:text-yellow-400 transition">★</button>
                    </div>
                    <input type="hidden" name="rating" id="rating_value" value="<?php echo e(old('rating')); ?>">
                    <p id="rating_error" class="text-red-500 text-xs mt-1 hidden">Please select a rating</p>
                </div>
                
                <!-- Category Dropdown -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Category *</label>
                    <div class="relative">
                        <button type="button" id="categoryButton" 
                                class="w-full px-3 py-2 text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-between">
                            <span id="selectedCategory" class="flex items-center gap-2">
                                <i class="fas fa-folder-open text-gray-500 dark:text-gray-400"></i>
                                <span id="selectedCategoryText" class="text-gray-700 dark:text-gray-200">Select Category</span>
                            </span>
                            <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400"></i>
                        </button>
                        
                        <div id="categoryDropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="bug">
                                <i class="fas fa-bug text-red-500 w-5"></i>
                                <span class="text-gray-700 dark:text-gray-200">Bug Report</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="feature">
                                <i class="fas fa-lightbulb text-yellow-500 w-5"></i>
                                <span class="text-gray-700 dark:text-gray-200">Feature Request</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="improvement">
                                <i class="fas fa-chart-line text-green-500 w-5"></i>
                                <span class="text-gray-700 dark:text-gray-200">Improvement Suggestion</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="experience">
                                <i class="fas fa-smile-wink text-blue-500 w-5"></i>
                                <span class="text-gray-700 dark:text-gray-200">User Experience</span>
                            </div>
                            <div class="dropdown-category px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="other">
                                <i class="fas fa-comment-dots text-purple-500 w-5"></i>
                                <span class="text-gray-700 dark:text-gray-200">Other</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="category_value" value="<?php echo e(old('category')); ?>">
                    <p id="category_error" class="text-red-500 text-xs mt-1 hidden">Please select a category</p>
                </div>
                
                <!-- Message -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Your Message *</label>
                    <textarea name="message" id="message" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Please share your feedback, suggestions, or report any issues..." required><?php echo e(old('message')); ?></textarea>
                    <p id="message_error" class="text-red-500 text-xs mt-1 hidden">Message must be at least 10 characters</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Minimum 10 characters
                    </p>
                </div>
                
                <div class="flex justify-end mt-6 gap-2">
                    <button type="submit" id="feedbackSubmitBtn" 
                        class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
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
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 gradient-feedback rounded-t-xl">
    <h3 class="font-semibold text-gray-800 dark:text-white">
        <i class="fas fa-history text-purple-600 dark:text-purple-400 mr-2"></i> Your Previous Feedback
    </h3>
</div>
        <div id="feedbacksList" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="feedback-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="<?php echo e($fb->id); ?>">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $fb->rating): ?>
                                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex items-center gap-1
                            <?php if($fb->category == 'bug'): ?> bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300
                            <?php elseif($fb->category == 'feature'): ?> bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300
                            <?php elseif($fb->category == 'improvement'): ?> bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300
                            <?php elseif($fb->category == 'experience'): ?> bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300
                            <?php else: ?> bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 <?php endif; ?>">
                            <?php if($fb->category == 'bug'): ?>
                                <i class="fas fa-bug text-xs"></i>
                            <?php elseif($fb->category == 'feature'): ?>
                                <i class="fas fa-lightbulb text-xs"></i>
                            <?php elseif($fb->category == 'improvement'): ?>
                                <i class="fas fa-chart-line text-xs"></i>
                            <?php elseif($fb->category == 'experience'): ?>
                                <i class="fas fa-smile-wink text-xs"></i>
                            <?php else: ?>
                                <i class="fas fa-comment-dots text-xs"></i>
                            <?php endif; ?>
                            <?php echo e(ucfirst($fb->category)); ?>

                        </span>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($fb->created_at->format('M d, Y')); ?></span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2"><?php echo e($fb->message); ?></p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                        <?php if($fb->status == 'pending'): ?> bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300
                        <?php elseif($fb->status == 'reviewed'): ?> bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300
                        <?php else: ?> bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 <?php endif; ?>">
                        <i class="fas 
                            <?php if($fb->status == 'pending'): ?> fa-clock
                            <?php elseif($fb->status == 'reviewed'): ?> fa-eye
                            <?php else: ?> fa-check <?php endif; ?> text-xs"></i>
                        <?php echo e(ucfirst($fb->status)); ?>

                    </span>
                    <?php if($fb->admin_response): ?>
                        <span class="text-xs text-blue-600 dark:text-blue-400">
                            <i class="fas fa-reply mr-1"></i> Admin responded
                        </span>
                    <?php endif; ?>
                </div>
                <?php if($fb->admin_response): ?>
                    <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-600 dark:text-blue-400">
                            <i class="fas fa-headset mr-1"></i> <strong>Admin Response:</strong> <?php echo e($fb->admin_response); ?>

                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-8 text-center text-gray-500 dark:text-gray-400" id="emptyFeedbacks">
                <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                <p>No feedback yet</p>
                <p class="text-xs mt-1">Share your thoughts using the form</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // ============ STAR RATING ============
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating_value');
    
    let currentRating = ratingInput.value || 0;
    if (currentRating > 0) {
        stars.forEach((star, index) => {
            if (index < currentRating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-300');
                star.classList.remove('dark:text-gray-600');
            }
        });
    }
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            currentRating = rating;
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                    s.classList.remove('dark:text-gray-600');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                    s.classList.add('dark:text-gray-600');
                }
            });
            
            document.getElementById('rating_error').classList.add('hidden');
        });
    });
    
    // ============ CATEGORY DROPDOWN ============
    const categoryButton = document.getElementById('categoryButton');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const selectedCategoryText = document.getElementById('selectedCategoryText');
    const categoryInput = document.getElementById('category_value');
    
    let currentCategory = categoryInput.value || '';
    if (currentCategory) {
        const selectedOption = document.querySelector(`.dropdown-category[data-value="${currentCategory}"]`);
        if (selectedOption) {
            const icon = selectedOption.querySelector('i').cloneNode(true);
            const text = selectedOption.querySelector('span').textContent;
            document.getElementById('selectedCategory').innerHTML = '';
            document.getElementById('selectedCategory').appendChild(icon);
            document.getElementById('selectedCategory').appendChild(document.createTextNode(' ' + text));
            selectedCategoryText.textContent = text;
        }
    }
    
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
            
            document.getElementById('selectedCategory').innerHTML = '';
            document.getElementById('selectedCategory').appendChild(icon);
            document.getElementById('selectedCategory').appendChild(document.createTextNode(' ' + text));
            selectedCategoryText.textContent = text;
            
            categoryInput.value = value;
            currentCategory = value;
            categoryDropdown.classList.add('hidden');
            
            document.getElementById('category_error').classList.add('hidden');
            
            document.querySelectorAll('.dropdown-category').forEach(i => {
                i.classList.remove('bg-purple-50', 'dark:bg-purple-900/30');
            });
            this.classList.add('bg-purple-50', 'dark:bg-purple-900/30');
        });
    });
    
    document.addEventListener('click', function(e) {
        if (categoryButton && categoryDropdown) {
            if (!categoryButton.contains(e.target) && !categoryDropdown.contains(e.target)) {
                categoryDropdown.classList.add('hidden');
            }
        }
    });
    
    // ============ AJAX FORM SUBMISSION ============
    const form = document.getElementById('feedbackForm');
    const feedbackBtn = document.getElementById('feedbackSubmitBtn');
    const feedbackBtnText = document.getElementById('feedbackBtnText');
    const feedbackBtnSpinner = document.getElementById('feedbackBtnSpinner');
    const alertMessage = document.getElementById('alertMessage');
    
    function showAlert(message, type) {
        alertMessage.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;
        alertMessage.className = `rounded-lg p-3 mb-4 ${type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-l-4 border-green-500' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-l-4 border-red-500'}`;
        alertMessage.classList.remove('hidden');
        
        setTimeout(() => {
            alertMessage.classList.add('hidden');
        }, 5000);
    }
    
    function validateForm() {
        let isValid = true;
        
        if (!currentRating || currentRating === 0) {
            document.getElementById('rating_error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('rating_error').classList.add('hidden');
        }
        
        if (!currentCategory) {
            document.getElementById('category_error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('category_error').classList.add('hidden');
        }
        
        const message = document.getElementById('message').value.trim();
        if (message.length < 10) {
            document.getElementById('message_error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('message_error').classList.add('hidden');
        }
        
        return isValid;
    }
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            showAlert('Please fix the errors above.', 'error');
            return;
        }
        
        // ✅ PREVENT DOUBLE SUBMIT
        if (feedbackBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        // Disable button and show loading
        feedbackBtn.disabled = true;
        feedbackBtnText.textContent = 'Submitting...';
        if (feedbackBtnSpinner) feedbackBtnSpinner.classList.remove('hidden');
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('<?php echo e(route("student.feedback.store")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Reset form
                currentRating = 0;
                ratingInput.value = '';
                stars.forEach(star => {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                    star.classList.add('dark:text-gray-600');
                });
                
                currentCategory = '';
                categoryInput.value = '';
                document.getElementById('selectedCategory').innerHTML = '<i class="fas fa-folder-open text-gray-500 dark:text-gray-400"></i><span id="selectedCategoryText" class="text-gray-700 dark:text-gray-200">Select Category</span>';
                
                document.getElementById('message').value = '';
                
                showAlert(data.message, 'success');
                
                if (data.feedback) {
                    addFeedbackToList(data.feedback);
                }
            } else {
                showAlert(data.message || 'Something went wrong', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Network error. Please try again.', 'error');
        } finally {
            // Re-enable button
            feedbackBtn.disabled = false;
            feedbackBtnText.textContent = 'Submit Feedback';
            if (feedbackBtnSpinner) feedbackBtnSpinner.classList.add('hidden');
        }
    });
    
    function addFeedbackToList(feedback) {
        const container = document.getElementById('feedbacksList');
        const emptyDiv = document.getElementById('emptyFeedbacks');
        
        if (emptyDiv) emptyDiv.remove();
        
        const date = new Date();
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        let categoryIcon = '', categoryColor = '', categoryBg = '';
        switch(feedback.category) {
            case 'bug':
                categoryIcon = 'fa-bug';
                categoryColor = 'text-red-700 dark:text-red-300';
                categoryBg = 'bg-red-100 dark:bg-red-900/50';
                break;
            case 'feature':
                categoryIcon = 'fa-lightbulb';
                categoryColor = 'text-yellow-700 dark:text-yellow-300';
                categoryBg = 'bg-yellow-100 dark:bg-yellow-900/50';
                break;
            case 'improvement':
                categoryIcon = 'fa-chart-line';
                categoryColor = 'text-green-700 dark:text-green-300';
                categoryBg = 'bg-green-100 dark:bg-green-900/50';
                break;
            case 'experience':
                categoryIcon = 'fa-smile-wink';
                categoryColor = 'text-blue-700 dark:text-blue-300';
                categoryBg = 'bg-blue-100 dark:bg-blue-900/50';
                break;
            default:
                categoryIcon = 'fa-comment-dots';
                categoryColor = 'text-purple-700 dark:text-purple-300';
                categoryBg = 'bg-purple-100 dark:bg-purple-900/50';
        }
        
        const starsHtml = Array(5).fill().map((_, i) => {
            if (i < feedback.rating) {
                return '<i class="fas fa-star text-yellow-500 text-sm"></i>';
            }
            return '<i class="far fa-star text-gray-300 dark:text-gray-600 text-sm"></i>';
        }).join('');
        
        const newFeedbackHtml = `
            <div class="feedback-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="${feedback.id}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex">
                            ${starsHtml}
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex items-center gap-1 ${categoryBg} ${categoryColor}">
                            <i class="fas ${categoryIcon} text-xs"></i>
                            ${feedback.category.charAt(0).toUpperCase() + feedback.category.slice(1)}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">${formattedDate}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">${escapeHtml(feedback.message)}</p>
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300">
                        <i class="fas fa-clock text-xs"></i>
                        Pending
                    </span>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('afterbegin', newFeedbackHtml);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/feedback.blade.php ENDPATH**/ ?>
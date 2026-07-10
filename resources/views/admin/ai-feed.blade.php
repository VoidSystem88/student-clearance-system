@extends('layouts.app')

@section('title', 'AI Training - Feed Data')
@section('header', 'AI Training Center')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">📚 Feed New Data to Void AI</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">Teach the AI by providing question and answer pairs.</p>
        
        <form id="feedForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Question / Keyword</label>
                <input type="text" id="question" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Example: How do I get my clearance?">
                <p class="text-xs text-gray-500 mt-1">The AI will respond when user asks something similar to this.</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Answer / Response</label>
                <textarea id="answer" rows="4" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Example: You can get your clearance by..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Language</label>
                    <select id="language" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <option value="en">English</option>
                        <option value="tl">Tagalog</option>
                        <option value="bisaya">Bisaya</option>
                        <option value="all">All Languages</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Category (Optional)</label>
                    <select id="category" class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <option value="general">General</option>
                        <option value="submission">Document Submission</option>
                        <option value="requirements">Requirements</option>
                        <option value="status">Status</option>
                        <option value="processing">Processing Time</option>
                        <option value="password">Password</option>
                        <option value="account">Account</option>
                        <option value="security">Security</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-database mr-2"></i> Feed to AI
            </button>
        </form>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">📋 Fed Data History</h3>
            <button id="clearData" class="text-red-500 text-sm hover:text-red-700">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
        <div id="dataList" class="space-y-2 max-h-96 overflow-y-auto">
            <!-- Fed data will appear here -->
        </div>
    </div>
</div>

<script>
// Load fed data from localStorage
function loadFedData() {
    const data = localStorage.getItem('voidAI_learned');
    if (data) {
        const parsed = JSON.parse(data);
        const container = document.getElementById('dataList');
        container.innerHTML = '';
        
        parsed.reverse().forEach((item, index) => {
            const date = new Date(item.timestamp);
            const dateStr = date.toLocaleString();
            
            container.innerHTML += `
                <div class="border-b dark:border-gray-700 py-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-sm text-blue-600">Q: ${item.q}</p>
                            <p class="text-sm mt-1">A: ${item.a.substring(0, 100)}${item.a.length > 100 ? '...' : ''}</p>
                            <p class="text-xs text-gray-500 mt-1">Used ${item.used} times | ${dateStr}</p>
                        </div>
                        <button onclick="deleteItem(${index})" class="text-red-500 text-sm ml-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        if (parsed.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">No data fed yet. Start teaching the AI!</p>';
        }
    } else {
        document.getElementById('dataList').innerHTML = '<p class="text-gray-500 text-center py-4">No data fed yet. Start teaching the AI!</p>';
    }
}

// Delete item
function deleteItem(index) {
    const data = localStorage.getItem('voidAI_learned');
    if (data) {
        let parsed = JSON.parse(data);
        parsed.splice(parsed.length - 1 - index, 1);
        localStorage.setItem('voidAI_learned', JSON.stringify(parsed));
        loadFedData();
        showToast('Data deleted!', 'success');
    }
}

// Feed form submission
document.getElementById('feedForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const question = document.getElementById('question').value.trim();
    const answer = document.getElementById('answer').value.trim();
    const language = document.getElementById('language').value;
    const category = document.getElementById('category').value;
    
    if (!question || !answer) {
        showToast('Please fill in both question and answer!', 'error');
        return;
    }
    
    // Load existing data
    let existing = localStorage.getItem('voidAI_learned');
    let learned = existing ? JSON.parse(existing) : [];
    
    // Add new data
    learned.push({
        q: question.toLowerCase(),
        a: answer,
        category: category,
        language: language,
        timestamp: new Date().toISOString(),
        used: 0
    });
    
    // Save
    localStorage.setItem('voidAI_learned', JSON.stringify(learned));
    
    // Clear form
    document.getElementById('question').value = '';
    document.getElementById('answer').value = '';
    
    // Reload list
    loadFedData();
    
    showToast('Data fed successfully! AI learned something new! 🎓', 'success');
});

// Clear all data
document.getElementById('clearData').addEventListener('click', function() {
    if (confirm('Are you sure? This will delete all fed data.')) {
        localStorage.removeItem('voidAI_learned');
        loadFedData();
        showToast('All data cleared!', 'info');
    }
});

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 z-50 px-4 py-2 rounded-lg text-white text-sm ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Load on page load
loadFedData();
</script>
@endsection
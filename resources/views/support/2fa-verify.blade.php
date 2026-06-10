<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>2FA Verification - Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-purple-400 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">2FA Verification</h2>
                <p class="text-gray-300 text-sm mt-2">Enter the 6-digit code sent to your email</p>
            </div>
            
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('support.2fa.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-300 text-sm mb-2 text-center">Verification Code</label>
                    <div class="flex justify-center gap-3">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="0">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="1">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="2">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="3">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="4">
                        <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold bg-white/10 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:border-purple-500" data-index="5">
                    </div>
                    <input type="hidden" name="code" id="code_input">
                </div>
                
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-check-circle mr-2"></i> Verify Code
                </button>
            </form>
            
            <div class="text-center mt-4">
                <button id="resendBtn" class="text-purple-400 hover:text-purple-300 text-sm transition">
                    <i class="fas fa-redo-alt mr-1"></i> Didn't receive code? Resend
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="/" class="text-gray-400 hover:text-gray-300 text-sm transition">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <div id="toast" class="fixed top-5 right-5 z-50 hidden"></div>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // OTP digit handling
        const digits = document.querySelectorAll('.otp-digit');
        
        digits.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && index < 5) {
                    digits[index + 1].focus();
                }
                updateCode();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    digits[index - 1].focus();
                }
            });
        });
        
        function updateCode() {
            let code = '';
            digits.forEach(d => code += d.value);
            document.getElementById('code_input').value = code;
        }
        
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.className = `fixed top-5 right-5 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            toast.innerHTML = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }
        
        // Resend code
        document.getElementById('resendBtn')?.addEventListener('click', async function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            try {
                const response = await fetch('{{ route("support.2fa.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('New verification code sent to your email!', 'success');
                    digits.forEach(d => d.value = '');
                    digits[0].focus();
                } else {
                    showToast(data.message || 'Failed to resend code', 'error');
                }
            } catch (error) {
                showToast('Network error. Please try again.', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-redo-alt mr-1"></i> Didn\'t receive code? Resend';
            }
        });
    </script>
</body>
</html>
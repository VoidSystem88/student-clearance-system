<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>2FA Verification - Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        .verification-card {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .otp-digit {
            transition: all 0.2s ease;
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        .otp-digit:focus {
            transform: scale(1.05);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e3a5f, #1e40af);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transform: translateY(-2px);
        }
        .resend-btn {
            color: #60a5fa;
        }
        .resend-btn:hover {
            color: #93c5fd;
        }
        .back-link {
            color: #9ca3af;
        }
        .back-link:hover {
            color: #d1d5db;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="verification-card max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-500/30">
                    <i class="fas fa-shield-alt text-blue-400 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">2FA Verification</h2>
                <p class="text-gray-400 text-sm mt-2">Enter the 6-digit code sent to your email</p>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700">
                <div class="p-8">
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('support.2fa.verify') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-300 text-sm mb-2 text-center">Verification Code</label>
                            <div class="flex justify-center gap-3">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="0">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="1">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="2">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="3">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="4">
                                <input type="text" maxlength="1" class="otp-digit w-12 h-12 text-center text-2xl font-bold border rounded-lg" data-index="5">
                            </div>
                            <input type="hidden" name="code" id="code_input">
                        </div>
                        
                        <button type="submit" class="btn-primary w-full text-white py-3 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle mr-2"></i> Verify Code
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <button id="resendBtn" class="resend-btn text-sm transition">
                            <i class="fas fa-redo-alt mr-1"></i> Didn't receive code? Resend
                        </button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="/" class="back-link text-sm transition">
                            ← Back to Home
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6 text-gray-500 text-xs">
                <p>© {{ date('Y') }} Void Clearance System | Secure Authentication</p>
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
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').slice(0, 6);
                const pastedChars = pastedData.split('');
                digits.forEach((digit, i) => {
                    if (pastedChars[i]) digit.value = pastedChars[i];
                });
                updateCode();
                if (digits[5] && digits[5].value) digits[5].focus();
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
        
        // Auto-focus on first input
        if (digits[0]) digits[0].focus();
    </script>
</body>
</html>
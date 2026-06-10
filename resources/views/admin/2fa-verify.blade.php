<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>2FA Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .verification-card {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .otp-input {
            transition: all 0.2s ease;
        }
        .otp-input:focus {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="verification-card max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur">
                    <i class="fas fa-shield-alt text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Secure Access</h1>
                <p class="text-white/70 text-sm">Two-Factor Authentication</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-800">Verify Your Identity</h2>
                            <p class="text-xs text-gray-500">Enter the 6-digit code sent to your email</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-3 rounded mb-4 text-sm">
                            <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.2fa.verify') }}" id="verifyForm">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Verification Code</label>
                            <div class="flex justify-center gap-2">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="0">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="1">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="2">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="3">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="4">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="5">
                            </div>
                            <input type="hidden" name="code" id="fullCode">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-check-circle mr-2"></i> Verify & Continue
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <button id="resendCodeBtn" class="text-sm text-blue-600 hover:text-blue-800 transition">
                            <i class="fas fa-envelope mr-1"></i> Didn't receive code? Resend
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6 text-white/60 text-xs">
                <p>© {{ date('Y') }} Clearance System | Secure Authentication</p>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const fullCodeInput = document.getElementById('fullCode');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateFullCode();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').slice(0, 6);
                const pastedChars = pastedData.split('');
                inputs.forEach((digit, i) => {
                    if (pastedChars[i]) digit.value = pastedChars[i];
                });
                updateFullCode();
                if (inputs[5] && inputs[5].value) inputs[5].focus();
            });
        });
        
        function updateFullCode() {
            let code = '';
            inputs.forEach(input => { code += input.value; });
            fullCodeInput.value = code;
        }
        
        document.getElementById('resendCodeBtn')?.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            fetch('{{ route("admin.2fa.resend") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ New verification code sent to your email!');
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                } else {
                    alert('✗ ' + (data.message || 'Failed to send code'));
                }
            })
            .catch(() => alert('✗ Network error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-envelope mr-1"></i> Didn\'t receive code? Resend';
            });
        });
    </script>
</body>
</html>
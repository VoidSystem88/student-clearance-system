<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>2FA Verification - Clearance System</title>
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
        .otp-input {
            transition: all 0.2s ease;
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        .otp-input:focus {
            transform: scale(1.05);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
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
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="verification-card max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur border border-blue-500/30">
                    <i class="fas fa-shield-alt text-3xl text-blue-400"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Secure Access</h1>
                <p class="text-gray-400 text-sm">Two-Factor Authentication</p>
            </div>
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-gray-700">
                <div class="px-6 py-5 border-b border-gray-700 bg-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-lock text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white">Verify Your Identity</h2>
                            <p class="text-xs text-gray-400">Enter the 6-digit code sent to your email</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if(session('error')): ?>
                        <div class="bg-red-500/20 border-l-4 border-red-500 text-red-300 p-3 rounded mb-4 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('info')): ?>
                        <div class="bg-blue-500/20 border-l-4 border-blue-500 text-blue-300 p-3 rounded mb-4 text-sm">
                            <i class="fas fa-info-circle mr-2"></i> <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo e(route('admin.2fa.verify')); ?>" id="verifyForm">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-5">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Verification Code</label>
                            <div class="flex justify-center gap-2">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="0">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="1">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="2">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="3">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="4">
                                <input type="text" class="otp-input w-14 h-14 text-center text-2xl font-bold border rounded-lg focus:outline-none" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="5">
                            </div>
                            <input type="hidden" name="code" id="fullCode">
                        </div>
                        
                        <button type="submit" class="btn-primary w-full text-white py-3 rounded-lg font-medium transition flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle mr-2"></i> Verify & Continue
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <button id="resendCodeBtn" class="resend-btn text-sm transition">
                            <i class="fas fa-envelope mr-1"></i> Didn't receive code? Resend
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6 text-gray-500 text-xs">
                <p>© <?php echo e(date('Y')); ?> Void Clearance System | Secure Authentication</p>
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
            
            fetch('<?php echo e(route("admin.2fa.resend")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/2fa-verify.blade.php ENDPATH**/ ?>
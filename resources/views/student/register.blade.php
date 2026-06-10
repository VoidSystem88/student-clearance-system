<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Clearance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .panel-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .panel-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 4px;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .input-error {
            border-color: #ef4444 !important;
        }
        .input-success {
            border-color: #10b981 !important;
        }
        .warning-message {
            color: #f59e0b;
            font-size: 0.75rem;
            margin-top: 4px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white text-center">Student Registration</h2>
            <p class="text-blue-100 text-center text-sm mt-1">Create your account</p>
        </div>

        <div class="p-6">
            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
                    <div class="text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form id="registerForm" method="POST" action="{{ route('student.register.submit') }}">
                @csrf

                <!-- Student ID -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Student ID *</label>
                    <input type="text" name="student_id" id="student_id" class="panel-input" value="{{ old('student_id') }}" required>
                    <div id="student_id_error" class="error-message"></div>
                </div>

                <!-- First Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="panel-input" value="{{ old('first_name') }}" required>
                    <div id="first_name_error" class="error-message"></div>
                </div>

                <!-- Last Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="panel-input" value="{{ old('last_name') }}" required>
                    <div id="last_name_error" class="error-message"></div>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" id="email" class="panel-input" value="{{ old('email') }}" required>
                    <div id="email_error" class="error-message"></div>
                </div>

                <!-- Course -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Course *</label>
                    <select name="course" id="course" class="panel-input" required>
                        <option value="">-- Select Course --</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIS">BSIS</option>
                        <option value="BSBA-FM">BSBA Financial Management</option>
                        <option value="BSHM">BS Hospitality Management</option>
                        <option value="BEEd">BEEd</option>
                        <option value="BSEd-English">BSEd - English</option>
                        <option value="BSCrim">BS Criminology</option>
                    </select>
                    <div id="course_error" class="error-message"></div>
                </div>

                <!-- Year Level -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Year Level *</label>
                    <select name="year_level" id="year_level" class="panel-input" required>
                        <option value="">-- Select Year --</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                    <div id="year_level_error" class="error-message"></div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Password *</label>
                    <input type="password" name="password" id="password" class="panel-input" required>
                    <div id="password_error" class="error-message"></div>
                    <div id="password_warning" class="warning-message"></div>
                    <div class="text-xs text-gray-500 mt-1">
                        <span id="length_check" class="block">✗ At least 8 characters</span>
                        <span id="number_check" class="block">✗ At least one number</span>
                        <span id="letter_check" class="block">✗ At least one letter</span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="panel-input" required>
                    <div id="confirm_error" class="error-message"></div>
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300">
                    Register
                </button>

                <p class="text-center text-gray-600 text-sm mt-4">
                    Already have an account? 
                    <a href="{{ route('student.login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login here</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    // Get form elements
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');

    // Password validation requirements
    function validatePassword() {
        const passwordValue = password.value;
        let isValid = true;

        // Check length (at least 8 characters)
        const lengthValid = passwordValue.length >= 8;
        document.getElementById('length_check').innerHTML = lengthValid ? '✓ At least 8 characters' : '✗ At least 8 characters';
        document.getElementById('length_check').style.color = lengthValid ? '#10b981' : '#ef4444';
        if (!lengthValid) isValid = false;

        // Check for number
        const numberValid = /\d/.test(passwordValue);
        document.getElementById('number_check').innerHTML = numberValid ? '✓ At least one number' : '✗ At least one number';
        document.getElementById('number_check').style.color = numberValid ? '#10b981' : '#ef4444';
        if (!numberValid) isValid = false;

        // Check for letter
        const letterValid = /[a-zA-Z]/.test(passwordValue);
        document.getElementById('letter_check').innerHTML = letterValid ? '✓ At least one letter' : '✗ At least one letter';
        document.getElementById('letter_check').style.color = letterValid ? '#10b981' : '#ef4444';
        if (!letterValid) isValid = false;

        // Show/hide warning
        const warningDiv = document.getElementById('password_warning');
        if (passwordValue.length > 0 && !isValid) {
            warningDiv.innerHTML = '⚠️ Password must be at least 8 characters with letters and numbers';
            warningDiv.style.display = 'block';
        } else {
            warningDiv.style.display = 'none';
        }

        // Highlight password field
        if (passwordValue.length > 0) {
            if (isValid) {
                password.classList.remove('input-error');
                password.classList.add('input-success');
            } else {
                password.classList.remove('input-success');
                password.classList.add('input-error');
            }
        } else {
            password.classList.remove('input-error', 'input-success');
        }

        return isValid;
    }

    // Validate confirm password
    function validateConfirmPassword() {
        const passwordValue = password.value;
        const confirmValue = confirmPassword.value;
        const errorDiv = document.getElementById('confirm_error');

        if (confirmValue.length > 0) {
            if (passwordValue === confirmValue) {
                errorDiv.innerHTML = '✓ Passwords match';
                errorDiv.style.color = '#10b981';
                errorDiv.classList.add('show');
                confirmPassword.classList.remove('input-error');
                confirmPassword.classList.add('input-success');
                return true;
            } else {
                errorDiv.innerHTML = '✗ Passwords do not match';
                errorDiv.style.color = '#ef4444';
                errorDiv.classList.add('show');
                confirmPassword.classList.remove('input-success');
                confirmPassword.classList.add('input-error');
                return false;
            }
        } else {
            errorDiv.classList.remove('show');
            confirmPassword.classList.remove('input-error', 'input-success');
            return false;
        }
    }

    // Validate required fields
    function validateRequiredFields() {
        const fields = ['student_id', 'first_name', 'last_name', 'email', 'course', 'year_level'];
        let allValid = true;

        fields.forEach(field => {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(`${field}_error`);
            
            if (input && input.value.trim() === '') {
                errorDiv.innerHTML = 'This field is required';
                errorDiv.classList.add('show');
                input.classList.add('input-error');
                allValid = false;
            } else if (input && field === 'email' && input.value.trim() !== '') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(input.value)) {
                    errorDiv.innerHTML = 'Please enter a valid email address';
                    errorDiv.classList.add('show');
                    input.classList.add('input-error');
                    allValid = false;
                } else {
                    errorDiv.classList.remove('show');
                    input.classList.remove('input-error');
                    input.classList.add('input-success');
                }
            } else if (input) {
                errorDiv.classList.remove('show');
                input.classList.remove('input-error');
                if (input.value.trim() !== '') {
                    input.classList.add('input-success');
                } else {
                    input.classList.remove('input-success');
                }
            }
        });

        return allValid;
    }

    // Real-time validation events
    password.addEventListener('input', () => {
        validatePassword();
        validateConfirmPassword();
        updateSubmitButton();
    });

    confirmPassword.addEventListener('input', () => {
        validateConfirmPassword();
        updateSubmitButton();
    });

    // Add validation to all required fields
    const requiredFields = ['student_id', 'first_name', 'last_name', 'email', 'course', 'year_level'];
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.addEventListener('input', () => {
                validateRequiredFields();
                updateSubmitButton();
            });
            element.addEventListener('change', () => {
                validateRequiredFields();
                updateSubmitButton();
            });
        }
    });

    // Update submit button state
    function updateSubmitButton() {
        const isPasswordValid = validatePassword();
        const isConfirmValid = validateConfirmPassword();
        const isRequiredValid = validateRequiredFields();
        
        const isEmailValid = () => {
            const email = document.getElementById('email');
            if (email && email.value.trim() !== '') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email.value);
            }
            return false;
        };

        const allValid = isPasswordValid && isConfirmValid && isRequiredValid && isEmailValid();
        
        if (allValid) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    // Form submit validation
    form.addEventListener('submit', function(e) {
        const isPasswordValid = validatePassword();
        const isConfirmValid = validateConfirmPassword();
        const isRequiredValid = validateRequiredFields();

        if (!isPasswordValid || !isConfirmValid || !isRequiredValid) {
            e.preventDefault();
            alert('Please fix all errors before submitting.');
        }
    });

    // Initial validation
    validateRequiredFields();
</script>

</body>
</html>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.signup-form');
    const passwordInput = form.querySelector('input[name="password"]');
    const confirmPasswordInput = form.querySelector('input[name="password_confirmation"]');
    const phoneInput = form.querySelector('input[name="phone"]');

    // Form Validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        if (validateForm()) {
            form.submit();
        }
    });

    // Password Validation with Real-time Feedback
    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        const strengthResult = validatePasswordStrength(password);
        updatePasswordRequirements(password);
    });

    // Password Confirmation Validation
    confirmPasswordInput.addEventListener('input', () => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('كلمة المرور غير متطابقة');
            showError(confirmPasswordInput, 'كلمة المرور غير متطابقة');
        } else {
            confirmPasswordInput.setCustomValidity('');
            hideError(confirmPasswordInput);
        }
    });

    // Phone Validation with International Format Support
    phoneInput.addEventListener('input', () => {
        let phone = phoneInput.value.trim();

        // Remove any non-digit characters except +
        phone = phone.replace(/[^\d+]/g, '');

        // Ensure only one + at the start
        if (phone.includes('+')) {
            phone = '+' + phone.replace(/\+/g, '');
        }

        phoneInput.value = phone;

        // Validate international phone number format (minimum 8 digits, maximum 15 digits)
        const phoneRegex = /^\+?(\d{8,15})$/;
        if (!phoneRegex.test(phone)) {
            const message = 'يجب إدخال رقم هاتف صحيح (8-15 رقم)';
            phoneInput.setCustomValidity(message);
            showError(phoneInput, message);
        } else {
            phoneInput.setCustomValidity('');
            hideError(phoneInput);
        }
    });

    // Add CSS for password strength indicator
    const style = document.createElement('style');
    style.textContent = `
        .password-requirements {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .password-requirements ul {
            list-style: none;
            padding-right: 0;
            margin-bottom: 0;
        }
        .password-requirements li {
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .password-requirements li i {
            font-size: 0.75rem;
        }
        .password-requirements li.valid i {
            color: #198754;
        }
        .password-requirements li.invalid i {
            color: #dc3545;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .input-group {
            position: relative;
            transition: all 0.3s ease;
        }
        .input-group.focused {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-radius: 0.375rem;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-start-start-radius: 0;
            border-end-start-radius: 0;
        }
        .invalid-feedback {
            display: block;
            margin-top: 0.25rem;
        }
    `;
    document.head.appendChild(style);

    // Floating Label Effect
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', () => {
            input.closest('.input-group').classList.add('focused');
        });

        input.addEventListener('blur', () => {
            input.closest('.input-group').classList.remove('focused');
        });
    });
});

// Update Password Requirements UI
function updatePasswordRequirements(password) {
    const requirements = [
        { regex: /.{8,}/, index: 0, text: '8 أحرف على الأقل' },
        { regex: /[A-Z]/, index: 1, text: 'حرف كبير (A-Z)' },
        { regex: /[a-z]/, index: 2, text: 'حرف صغير (a-z)' },
        { regex: /[0-9]/, index: 3, text: 'رقم (0-9)' },
        { regex: /[!@#$%^&*(),.?":{}|<>]/, index: 4, text: 'رمز خاص' }
    ];

    const requirementsList = document.querySelectorAll('.password-requirements li');
    let allValid = true;

    requirements.forEach(req => {
        const li = requirementsList[req.index];
        const icon = li.querySelector('i');
        const isValid = req.regex.test(password);

        if (password.length === 0) {
            icon.className = 'fas fa-circle text-muted';
            li.className = '';
        } else {
            icon.className = isValid
                ? 'fas fa-check-circle'
                : 'fas fa-times-circle';
            li.className = isValid ? 'valid' : 'invalid';
        }

        allValid = allValid && isValid;
    });

    const passwordInput = document.querySelector('input[name="password"]');
    if (allValid && password.length > 0) {
        passwordInput.setCustomValidity('');
    } else if (password.length > 0) {
        passwordInput.setCustomValidity('يرجى تحقيق جميع متطلبات كلمة المرور');
    }
}

// Form Validation Function
function validateForm() {
    const form = document.querySelector('.signup-form');
    const password = form.querySelector('input[name="password"]').value;
    const confirmPassword = form.querySelector('input[name="password_confirmation"]').value;
    const phone = form.querySelector('input[name="phone"]').value;
    let isValid = true;

    // Password match validation
    if (password !== confirmPassword) {
        showToast('كلمة المرور غير متطابقة', 'error');
        isValid = false;
    }

    // Password strength validation
    const strengthResult = validatePasswordStrength(password);
    if (!strengthResult.isValid) {
        showToast(strengthResult.message, 'error');
        isValid = false;
    }

    // International phone number validation
    const phoneRegex = /^\+?(\d{8,15})$/;
    if (!phoneRegex.test(phone)) {
        showToast('يجب إدخال رقم هاتف صحيح', 'error');
        isValid = false;
    }

    return isValid;
}

// Password Strength Validation
function validatePasswordStrength(password) {
    if (password.length < 8) {
        return {
            isValid: false,
            message: 'كلمة المرور يجب أن تكون 8 أحرف على الأقل'
        };
    }

    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    if (!(hasUpperCase && hasLowerCase && hasNumbers)) {
        return {
            isValid: false,
            message: 'كلمة المرور يجب أن تحتوي على حروف كبيرة وصغيرة وأرقام'
        };
    }

    if (!hasSpecialChar) {
        return {
            isValid: false,
            message: 'كلمة المرور يجب أن تحتوي على رمز خاص واحد على الأقل'
        };
    }

    return {
        isValid: true,
        message: ''
    };
}

// Show Error Message
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    input.classList.add('is-invalid');

    let errorDiv = formGroup.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        formGroup.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

// Hide Error Message
function hideError(input) {
    const formGroup = input.closest('.form-group');
    input.classList.remove('is-invalid');

    const errorDiv = formGroup.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Toast Notification
function showToast(message, type = 'success') {
    const toastHTML = `
        <div class="toast-container position-fixed top-0 start-0 p-3">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'نجاح' : 'خطأ'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = document.querySelector('.toast:last-child');
    const toast = new bootstrap.Toast(toastElement, {
        delay: 3000,
        animation: true
    });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function () {
        this.parentElement.remove();
    });
}

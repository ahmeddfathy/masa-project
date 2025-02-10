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

    // Password Validation
    confirmPasswordInput.addEventListener('input', () => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('كلمة المرور غير متطابقة');
            showError(confirmPasswordInput, 'كلمة المرور غير متطابقة');
        } else {
            confirmPasswordInput.setCustomValidity('');
            hideError(confirmPasswordInput);
        }
    });

    // Phone Validation
    phoneInput.addEventListener('input', () => {
        const phoneRegex = /^05\d{8}$/;
        if (!phoneRegex.test(phoneInput.value)) {
            phoneInput.setCustomValidity('رقم الهاتف غير صحيح - يجب أن يبدأ ب 05 ويتكون من 10 أرقام');
            showError(phoneInput, 'رقم الهاتف غير صحيح - يجب أن يبدأ ب 05 ويتكون من 10 أرقام');
        } else {
            phoneInput.setCustomValidity('');
            hideError(phoneInput);
        }
    });

    // Password Strength Validation
    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        const strengthResult = validatePasswordStrength(password);

        if (!strengthResult.isValid) {
            passwordInput.setCustomValidity(strengthResult.message);
            showError(passwordInput, strengthResult.message);
        } else {
            passwordInput.setCustomValidity('');
            hideError(passwordInput);
        }
    });

    // Floating Label Effect
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.classList.remove('focused');
            }
        });

        // Check on page load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
});

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

    // Phone number validation
    const phoneRegex = /^05\d{8}$/;
    if (!phoneRegex.test(phone)) {
        showToast('رقم الهاتف غير صحيح', 'error');
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
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'نجاح' : 'خطأ'}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
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
        delay: 3000
    });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function () {
        this.parentElement.remove();
    });
}

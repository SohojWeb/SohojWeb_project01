document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('signupForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    const passwordStrength = document.getElementById('passwordStrength');
    const toast = document.getElementById('toast');

    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        updatePasswordStrength(password);
    });

    function updatePasswordStrength(password) {
        const segments = passwordStrength.querySelectorAll('.strength-segment');
        const strengthText = passwordStrength.querySelector('.strength-text');
        
        let strength = 0;
        let label = '';
        let className = '';

        if (password.length === 0) {
            segments.forEach(seg => {
                seg.classList.remove('active', 'weak', 'fair', 'good', 'strong');
            });
            strengthText.textContent = '';
            strengthText.className = 'strength-text';
            return;
        }

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        switch (strength) {
            case 1:
                label = 'Weak';
                className = 'weak';
                break;
            case 2:
                label = 'Fair';
                className = 'fair';
                break;
            case 3:
                label = 'Good';
                className = 'good';
                break;
            case 4:
                label = 'Strong';
                className = 'strong';
                break;
            default:
                label = password.length < 8 ? 'Too short' : 'Weak';
                className = 'weak';
        }

        const activeCount = Math.max(1, Math.min(strength, 4));
        
        segments.forEach((seg, index) => {
            seg.classList.remove('active', 'weak', 'fair', 'good', 'strong');
            if (index < activeCount) {
                seg.classList.add('active', className);
            }
        });

        strengthText.textContent = label;
        strengthText.className = `strength-text ${className}`;
    }

    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        const errorSpan = formGroup.querySelector('.error-message');
        const inputWrapper = formGroup.querySelector('.input-wrapper');
        
        inputWrapper.classList.remove('success');
        inputWrapper.classList.add('error');
        errorSpan.textContent = message;
        errorSpan.classList.add('show');
    }

    function showSuccess(input) {
        const formGroup = input.closest('.form-group');
        const errorSpan = formGroup.querySelector('.error-message');
        const inputWrapper = formGroup.querySelector('.input-wrapper');
        
        inputWrapper.classList.remove('error');
        inputWrapper.classList.add('success');
        errorSpan.textContent = '';
        errorSpan.classList.remove('show');
    }

    function clearError(input) {
        const formGroup = input.closest('.form-group');
        const errorSpan = formGroup.querySelector('.error-message');
        const inputWrapper = formGroup.querySelector('.input-wrapper');
        
        inputWrapper.classList.remove('error', 'success');
        errorSpan.textContent = '';
        errorSpan.classList.remove('show');
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePassword(password) {
        return password.length >= 8 && 
               /[A-Z]/.test(password) && 
               /[0-9]/.test(password);
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        let isValid = true;
        
        const firstName = document.getElementById('firstName');
        const lastName = document.getElementById('lastName');
        const email = document.getElementById('email');
        const terms = document.getElementById('terms');
        
        clearError(firstName);
        clearError(lastName);
        clearError(email);
        clearError(passwordInput);
        clearError(confirmPasswordInput);
        
        if (firstName.value.trim() === '') {
            showError(firstName, 'First name is required');
            isValid = false;
        } else {
            showSuccess(firstName);
        }
        
        if (lastName.value.trim() === '') {
            showError(lastName, 'Last name is required');
            isValid = false;
        } else {
            showSuccess(lastName);
        }
        
        if (email.value.trim() === '') {
            showError(email, 'Email is required');
            isValid = false;
        } else if (!validateEmail(email.value)) {
            showError(email, 'Please enter a valid email');
            isValid = false;
        } else {
            showSuccess(email);
        }
        
        if (passwordInput.value === '') {
            showError(passwordInput, 'Password is required');
            isValid = false;
        } else if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, 'Password must be 8+ chars with uppercase & number');
            isValid = false;
        } else {
            showSuccess(passwordInput);
        }
        
        if (confirmPasswordInput.value === '') {
            showError(confirmPasswordInput, 'Please confirm your password');
            isValid = false;
        } else if (confirmPasswordInput.value !== passwordInput.value) {
            showError(confirmPasswordInput, 'Passwords do not match');
            isValid = false;
        } else {
            showSuccess(confirmPasswordInput);
        }
        
        const termsGroup = terms.closest('.form-group');
        const termsError = termsGroup.querySelector('.error-message');
        if (!terms.checked) {
            termsError.textContent = 'You must agree to the terms';
            termsError.classList.add('show');
            isValid = false;
        } else {
            termsError.textContent = '';
            termsError.classList.remove('show');
        }
        
        if (isValid) {
            const submitBtn = form.querySelector('.submit-btn');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
                
                showToast();
                
                form.reset();
                clearAllStates();
            }, 1500);
        }
    });

    function clearAllStates() {
        const inputWrappers = form.querySelectorAll('.input-wrapper');
        const errorMessages = form.querySelectorAll('.error-message');
        const segments = passwordStrength.querySelectorAll('.strength-segment');
        const strengthText = passwordStrength.querySelector('.strength-text');
        
        inputWrappers.forEach(wrapper => {
            wrapper.classList.remove('error', 'success');
        });
        
        errorMessages.forEach(msg => {
            msg.classList.remove('show');
        });
        
        segments.forEach(seg => {
            seg.classList.remove('active', 'weak', 'fair', 'good', 'strong');
        });
        
        strengthText.textContent = '';
        strengthText.className = 'strength-text';
    }

    function showToast() {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const platform = btn.classList.contains('google') ? 'Google' :
                           btn.classList.contains('github') ? 'GitHub' : 'Twitter';
            console.log(`Continue with ${platform}`);
        });
    });

    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('blur', () => {
            if (input.value.trim() !== '' && !input.closest('.checkbox-group')) {
                input.dispatchEvent(new Event('input'));
            }
        });
    });
});

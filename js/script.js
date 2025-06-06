// script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.querySelector('.password-toggle');
    const requirements = document.querySelectorAll('.requirement');
    
    // Password visibility toggle
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type');
        passwordInput.setAttribute('type', type === 'password' ? 'text' : 'password');
        passwordToggle.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Password validation
    const patterns = {
        length: /.{8,}/,
        case: /(?=.*[a-z])(?=.*[A-Z])/,
        number: /\d/,
        symbol: /[!@#$%^&*(),.?":{}|<>]/
    };

    function validatePassword() {
        const password = passwordInput.value;
        
        // Check each requirement
        requirements.forEach((req, index) => {
            const bullet = req.querySelector('.bullet');
            let isValid = false;

            switch(index) {
                case 0:
                    isValid = patterns.length.test(password);
                    break;
                case 1:
                    isValid = patterns.case.test(password);
                    break;
                case 2:
                    isValid = patterns.number.test(password);
                    break;
                case 3:
                    isValid = patterns.symbol.test(password);
                    break;
            }

            req.classList.toggle('valid', isValid);
            bullet.style.background = isValid ? 'var(--success-color)' : '#666';
        });
    }

    passwordInput.addEventListener('input', validatePassword);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const password = passwordInput.value;

        // Validate all fields
        let isValid = true;
        let allRequirementsMet = true;

        requirements.forEach(req => {
            if (!req.classList.contains('valid')) {
                allRequirementsMet = false;
            }
        });

        if (!email || !password || !allRequirementsMet) {
            isValid = false;
        }

        if (isValid) {
            // Simulate API call
            console.log('Form submitted:', { email, phone, password });
            
            // Add success animation to button
            const submitButton = form.querySelector('.submit-button');
            submitButton.textContent = 'Success!';
            submitButton.style.background = 'var(--success-color)';
            
            setTimeout(() => {
                submitButton.textContent = 'Sign up';
                submitButton.style.background = 'var(--primary-color)';
            }, 2000);
        }
    });

    // Input animations
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
        });
    });
});
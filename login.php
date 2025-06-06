<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agromate-login</title>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <style>
        /* styles.css */
:root {
    --primary-color: #000000;
    --text-color: #333333;
    --border-color: #E5E5E5;
    /* --background-color: #1a1a1a; */
    --card-background: #FFFFFF;
    --input-background: #FFFFFF;
    --error-color: #FF3B30;
    --success-color: #34C759;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

body {
    min-height: 100vh;
    background:url(main/images/login-Image.jpg);
    background-size: cover;
    color: var(--text-color);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.container {
    width: 100%;
    max-width: 480px;
    text-align: center;
}

.logo {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.logo sup {
    font-size: 0.6rem;
    color: #FF3B30;
}

.main-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 500;
    margin-bottom: 2rem;
}

.card {
    background: var(--card-background);
    border-radius: 24px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: slideUp 0.5s ease-out;
}

.form-title {
    font-size: 1.75rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: #666;
    margin-bottom: 2rem;
    font-size: 0.95rem;
}

.form-group {
    margin-bottom: 1.5rem;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-color);
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
}

.input-hint {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.5rem;
}

.password-input-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 4px;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.password-requirements {
    margin-bottom: 2rem;
}

.requirement {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.bullet {
    width: 6px;
    height: 6px;
    background: #666;
    border-radius: 50%;
    margin-right: 8px;
}

.requirement.valid .bullet {
    background: var(--success-color);
}

.submit-button {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 999px;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease, background-color 0.2s ease;
}

.submit-button:hover {
    background: #333;
}

.submit-button:active {
    transform: scale(0.98);
}

.login-link {
    margin-top: 1.5rem;
    font-size: 0.95rem;
}

.text-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.text-link:hover {
    text-decoration: underline;
}

#alertBox {
    display: none;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: var(--error-color);
    color: white;
    padding: 15px;
    border-radius: 8px;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 80%;
    text-align: center;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 480px) {
    .card {
        padding: 1.5rem;
        border-radius: 16px;
    }

    .form-title {
        font-size: 1.5rem;
    }

    .form-subtitle {
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div id="alertBox"></div>
        
        <div class="card">
            <div class="card-content">
                <h2 class="form-title">Login to account</h2>
                <p class="form-subtitle">Agromate</p>
                
                <form id="signupForm" class="signup-form" action="action/login_action.php" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="username" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" name="password" id="password" required class="form-input">
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                Hide
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-button">Log in</button>
                </form>

                <p class="login-link">
                    Don't have an account? <a href="index.html" class="text-link">Sign up</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        // Function to show the centered alert box
        function showAlert(message) {
            var alertBox = document.getElementById("alertBox");
            alertBox.innerHTML = message;
            alertBox.style.display = "block";

            // Hide the alert box after 3 seconds (adjust as needed)
            setTimeout(function() {
                alertBox.style.display = "none";
            }, 2000);
        }

        // Check if there's an error message in the URL parameters
        var errorMessage = "<?php echo isset($_GET['error']) ? $_GET['error'] : ''; ?>";
        if (errorMessage !== "") {
            showAlert(errorMessage);
        }
    </script>
</body>
</html>
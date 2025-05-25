<?php
// app/views/auth/login.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventPro</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover: #0056b3;
            --error-color: #dc3545;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
        }

        .login-box {
            background: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .login-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-color);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: var(--primary-hover);
        }

        .error-message {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 1.2rem;
            padding: 10px;
            background: rgba(220, 53, 69, 0.1);
            border-radius: var(--border-radius);
        }

        .additional-links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .additional-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .additional-links a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 1.5rem;
            }
        }
        .logo img{
            height: 250px;
            width: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="logo">
            <img src="./event-pro.png">
        </div>
    <div class="login-container">
        <div class="login-box">
            <h2>Welcome to EventPro</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/index.php?url=auth/login" autocomplete="on">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
   
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email" 
                        required
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required
                        minlength="8"
                    >
                    <a href="/auth/forgot-password" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit">Log In</button>
            </form>

            <div class="additional-links">
                Don't have an account? <a href="index.php?url=auth/register">Sign Up</a>

            </div>
        </div>
    </div>

    <script>
        // Focus on email field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>
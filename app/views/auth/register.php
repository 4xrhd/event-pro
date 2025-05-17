<!DOCTYPE html>
<html>
<head>
    <title>Register - EventPro</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e9f0f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .register-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 1rem;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            margin-top: 10px;
            cursor: pointer;
        }

        button:hover {
            background: #1e7e34;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Create Account</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <a class="login-link" href="/auth/login">Already have an account? Login</a>
</div>

</body>
</html>

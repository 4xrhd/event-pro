<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Login');
?>

<div class="form-card" style="max-width: 440px;">
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <img src="<?= asset('event-pro.png') ?>" alt="EventPro Logo" style="height: 48px; width: auto; object-fit: contain; margin-bottom: 0.75rem; border-radius: 6px;">
        <h1 class="page-title">Welcome Back</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Log in to manage events and access your tickets</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('auth/login') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@domain.com" required autofocus
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 0.75rem;">Log In</button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
        Don't have an account? <a href="<?= url('auth/register') ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign Up</a>
    </div>
</div>

<?php renderFooter(); ?>

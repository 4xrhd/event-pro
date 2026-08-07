<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Register');
?>

<div class="form-card" style="max-width: 480px;">
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <img src="<?= asset('event-pro.png') ?>" alt="EventPro Logo" style="height: 48px; width: auto; object-fit: contain; margin-bottom: 0.75rem; border-radius: 6px;">
        <h1 class="page-title">Create Account</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Join EventPro to book tickets and manage events</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('auth/register') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="name" class="form-label">Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required autofocus
                   value="<?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@domain.com" required
                   value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Min 8 chars, 1 uppercase, 1 number, 1 special" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required minlength="8">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 0.75rem;">Create Account</button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
        Already have an account? <a href="<?= url('auth/login') ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">Log In</a>
    </div>
</div>

<?php renderFooter(); ?>

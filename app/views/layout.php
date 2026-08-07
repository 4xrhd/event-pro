<?php
// Shared Header Partial
function renderHeader($title = 'EventPro') {
    $isLoggedIn = isLoggedIn();
    $userName = $_SESSION['user_name'] ?? 'User';
    $userRole = getCurrentUserRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - EventPro</title>
    <link rel="icon" type="image/png" href="<?= asset('event-pro.png') ?>">
    <link rel="shortcut icon" href="<?= asset('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?= url('events') ?>" class="nav-brand">
                <img src="<?= asset('event-pro.png') ?>" alt="EventPro Logo" style="height: 32px; width: auto; object-fit: contain; border-radius: 4px;">
                <span>EventPro</span>
            </a>
            <ul class="nav-links">
                <li><a href="<?= url('events') ?>" class="nav-link">Events</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="<?= url('tickets/my-tickets') ?>" class="nav-link">My Tickets</a></li>
                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="<?= url('events/create') ?>" class="nav-link">Create Event</a></li>
                    <?php endif; ?>
                    <?php if ($userRole === 'admin' || $userRole === 'staff'): ?>
                        <li><a href="<?= url('tickets/checkin') ?>" class="nav-link">Staff Check-In</a></li>
                    <?php endif; ?>
                    <li class="user-badge">
                        👤 <?= htmlspecialchars($userName) ?>
                        <span class="role-tag role-<?= htmlspecialchars($userRole ?: 'customer') ?>">
                            <?= htmlspecialchars($userRole ?: 'customer') ?>
                        </span>
                    </li>
                    <li><a href="<?= url('auth/logout') ?>" class="nav-link btn-sm btn-secondary">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= url('auth/login') ?>" class="nav-link">Login</a></li>
                    <li><a href="<?= url('auth/register') ?>" class="btn btn-sm btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">
<?php
}

function renderFooter() {
?>
    </main>
    <footer class="footer">
        &copy; <?= date('Y') ?> EventPro. All rights reserved. Simplifying event management one ticket at a time.
    </footer>
</body>
</html>
<?php
}

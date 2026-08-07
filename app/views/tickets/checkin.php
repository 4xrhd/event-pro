<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Staff Ticket Check-In');
?>

<div class="form-card" style="max-width: 580px;">
    <h1 class="page-title" style="text-align: center; margin-bottom: 0.5rem;">📲 Staff Check-In Dashboard</h1>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 1.5rem;">Validate attendee tickets and mark them checked-in in real-time.</p>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('tickets/checkin') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="ticket_id" class="form-label">Ticket ID or QR Code Data *</label>
            <input type="number" id="ticket_id" name="ticket_id" class="form-control" placeholder="Enter Ticket # (e.g. 101)" required autofocus style="font-size: 1.2rem; padding: 0.85rem; text-align: center;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1.05rem;">
            ✅ Validate & Check-In Attendee
        </button>
    </form>
</div>

<?php renderFooter(); ?>

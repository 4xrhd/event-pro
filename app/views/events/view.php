<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader(htmlspecialchars($event['title'] ?? 'Event Details'));
$userRole = getCurrentUserRole();
$booked = (int) ($event['booked_tickets'] ?? 0);
$capacity = isset($event['capacity']) && $event['capacity'] !== null ? (int) $event['capacity'] : null;
$isSoldOut = $capacity !== null && $booked >= $capacity;
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= url('events') ?>" class="btn btn-secondary btn-sm">← Back to Events</a>
    </div>

    <div class="card" style="padding: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
            <h1 class="page-title"><?= htmlspecialchars($event['title']) ?></h1>
            <span class="badge badge-price" style="font-size: 1.25rem;">$<?= number_format($event['price'], 2) ?></span>
        </div>

        <div class="card-meta" style="font-size: 1rem; gap: 0.75rem; margin-bottom: 1.5rem;">
            <div class="card-meta-item">
                📅 <strong>Date & Time:</strong> <?= date('F j, Y \a\t g:i a', strtotime($event['event_date'])) ?>
            </div>
            <div class="card-meta-item">
                📍 <strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?>
            </div>
            <div class="card-meta-item">
                🎟️ <strong>Capacity:</strong> <?= $booked ?> <?= $capacity !== null ? "/ {$capacity} booked" : "tickets booked (Unlimited capacity)" ?>
                <?php if ($isSoldOut): ?>
                    <span class="badge badge-danger" style="margin-left: 0.5rem;">Sold Out</span>
                <?php endif; ?>
            </div>
            <div class="card-meta-item">
                📌 <strong>Status:</strong> 
                <span class="badge badge-<?= $event['status'] === 'published' ? 'success' : 'warning' ?>">
                    <?= htmlspecialchars(ucfirst($event['status'])) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($event['description'])): ?>
            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--text-main);">About This Event</h3>
                <p style="color: var(--text-muted); line-height: 1.7; white-space: pre-line;"><?= htmlspecialchars($event['description']) ?></p>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <?php if (!$isSoldOut): ?>
                <a href="<?= url('tickets/book/' . $event['id']) ?>" class="btn btn-primary" style="flex: 1;">🎫 Book Tickets Now</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled style="flex: 1; opacity: 0.6;">Sold Out</button>
            <?php endif; ?>

            <?php if ($userRole === 'admin'): ?>
                <a href="<?= url('events/edit/' . $event['id']) ?>" class="btn btn-secondary">Edit Event</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php renderFooter(); ?>
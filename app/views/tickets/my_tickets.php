<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('My Tickets');
?>

<div class="page-header">
    <div>
        <h1 class="page-title">My Booked Tickets</h1>
        <p style="color: var(--text-muted);">View and download your purchased tickets and event access passes.</p>
    </div>
</div>

<?php if (!empty($tickets)): ?>
    <div class="grid">
        <?php foreach ($tickets as $ticket): ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <h2 class="card-title"><?= htmlspecialchars($ticket['event_title'] ?? 'Event Ticket') ?></h2>
                    <span class="badge badge-<?= $ticket['is_checked_in'] ? 'warning' : 'success' ?>">
                        <?= $ticket['is_checked_in'] ? 'Checked In' : 'Valid Pass' ?>
                    </span>
                </div>

                <div class="card-meta">
                    <div class="card-meta-item">
                        🎫 <strong>Ticket ID:</strong> #<?= (int) $ticket['id'] ?>
                    </div>
                    <div class="card-meta-item">
                        📅 <strong>Date:</strong> <?= date('F j, Y \a\t g:i a', strtotime($ticket['event_date'])) ?>
                    </div>
                    <div class="card-meta-item">
                        📍 <strong>Venue:</strong> <?= htmlspecialchars($ticket['venue']) ?>
                    </div>
                    <div class="card-meta-item">
                        🎟️ <strong>Quantity:</strong> <?= (int) $ticket['quantity'] ?> ticket(s)
                    </div>
                    <div class="card-meta-item">
                        💵 <strong>Total Amount:</strong> $<?= number_format($ticket['total_amount'], 2) ?>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="<?= url('tickets/confirm/' . (int) $ticket['id']) ?>" class="btn btn-sm btn-secondary">
                        View Ticket
                    </a>
                    <a href="<?= url('tickets/download/' . (int) $ticket['id']) ?>" class="btn btn-sm btn-primary" target="_blank" style="margin-left: auto;">
                        📄 PDF Pass
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        🎫
        <h3>No Tickets Found</h3>
        <p>You haven't booked any tickets yet. Explore upcoming events and reserve your spot!</p>
        <a href="<?= url('events') ?>" class="btn btn-primary">Browse Events</a>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>

<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Booking Confirmed');
?>

<div class="form-card" style="max-width: 520px; text-align: center;">
    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
    <h1 class="page-title" style="color: var(--success); margin-bottom: 0.5rem;">Ticket Confirmed!</h1>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Your booking was successful. Below is your official ticket details and QR code.</p>

    <div style="background: #f8fafc; border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; text-align: left;">
        <div style="text-align: center; margin-bottom: 1.25rem;">
            <img src="<?= htmlspecialchars($qrPath) ?>" alt="Ticket QR Code" style="width: 160px; height: 160px; border-radius: 8px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Scan this QR code at entry</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem;">
            <div><strong>Ticket ID:</strong> #<?= (int) $ticketId ?></div>
            <div><strong>Attendee:</strong> <?= htmlspecialchars($name) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($email) ?></div>
            <div><strong>Event:</strong> <?= htmlspecialchars($event['title']) ?></div>
            <div><strong>Date:</strong> <?= date('F j, Y \a\t g:i a', strtotime($event['event_date'])) ?></div>
            <div><strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></div>
            <div><strong>Quantity:</strong> <?= (int) $quantity ?> ticket(s)</div>
            <div><strong>Total Paid:</strong> <span style="color: var(--success); font-weight: 700;">$<?= number_format($total, 2) ?></span></div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a class="btn btn-primary" href="<?= url('tickets/download/' . (int) $ticketId) ?>" target="_blank">
            📄 Download Official PDF Ticket
        </a>
        <a class="btn btn-secondary" href="<?= url('tickets/my-tickets') ?>">
            View My Tickets
        </a>
    </div>
</div>

<?php renderFooter(); ?>

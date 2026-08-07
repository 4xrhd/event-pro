<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Book Tickets');
?>

<div class="form-card" style="max-width: 580px;">
    <h1 class="page-title" style="text-align: center; margin-bottom: 1rem;">🎫 Book Tickets</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div style="background: var(--primary-light); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid #c7d2fe;">
        <h3 style="color: var(--primary); font-size: 1.15rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($event['title']) ?></h3>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.25rem;">📅 <strong>Date:</strong> <?= date('F j, Y \a\t g:i a', strtotime($event['event_date'])) ?></p>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.25rem;">📍 <strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">💵 <strong>Price:</strong> $<span id="unit-price"><?= number_format($event['price'], 2) ?></span> per ticket</p>
        
        <?php if ($remaining !== null): ?>
            <p style="font-size: 0.85rem; font-weight: 600; color: #b45309;">⚠️ Remaining Capacity: <?= $remaining ?> ticket(s)</p>
        <?php endif; ?>
    </div>

    <form id="booking-form" method="POST" action="<?= url('tickets/book/' . (int) $eventId) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="name" class="form-label">Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe"
                   value="<?= htmlspecialchars($oldInput['name'] ?? ($_SESSION['user_name'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="john@example.com"
                   value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="quantity" class="form-label">Number of Tickets *</label>
            <select id="quantity" name="quantity" class="form-control" required>
                <?php 
                    $maxQty = $remaining !== null ? min(10, $remaining) : 10;
                    $maxQty = max(1, $maxQty);
                    for ($i = 1; $i <= $maxQty; $i++): 
                ?>
                    <option value="<?= $i ?>" <?= (int) ($oldInput['quantity'] ?? 1) === $i ? 'selected' : '' ?>><?= $i ?> ticket<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div style="background: #f1f5f9; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; color: var(--text-main);">Total Amount:</span>
            <span style="font-size: 1.35rem; font-weight: 700; color: var(--success);">$<span id="total-price"><?= number_format((float) ($event['price'] ?? 0) * (int) ($oldInput['quantity'] ?? 1), 2) ?></span></span>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Confirm & Generate Ticket</button>
            <a href="<?= url('events') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const unitPriceStr = document.getElementById("unit-price").innerText.replace(/,/g, '');
        const pricePerTicket = parseFloat(unitPriceStr) || 0;
        const quantitySelect = document.getElementById("quantity");
        const totalPriceSpan = document.getElementById("total-price");

        quantitySelect.addEventListener("change", function() {
            const qty = parseInt(this.value) || 1;
            totalPriceSpan.textContent = (qty * pricePerTicket).toFixed(2);
        });
    });
</script>

<?php renderFooter(); ?>

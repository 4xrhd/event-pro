<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Upcoming Events');
$userRole = getCurrentUserRole();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Upcoming Events</h1>
        <p style="color: var(--text-muted);">Discover and book tickets for upcoming conferences, workshops, and festivals.</p>
    </div>
    <?php if ($userRole === 'admin'): ?>
        <a href="<?= url('events/create') ?>" class="btn btn-primary">➕ Create New Event</a>
    <?php endif; ?>
</div>

<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success">
        ✅ <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger">
        ⚠️ <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<?php if (!empty($events)): ?>
    <div class="grid">
        <?php foreach ($events as $event): ?>
            <?php 
                $booked = (int) ($event['booked_tickets'] ?? 0);
                $capacity = isset($event['capacity']) && $event['capacity'] !== null ? (int) $event['capacity'] : null;
                $isSoldOut = $capacity !== null && $booked >= $capacity;
            ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <h2 class="card-title"><?= htmlspecialchars($event['title'] ?? 'Untitled Event') ?></h2>
                    <span class="badge badge-price">$<?= number_format($event['price'] ?? 0, 2) ?></span>
                </div>

                <div class="card-meta">
                    <div class="card-meta-item">
                        📅 <strong>Date:</strong> <?= date('F j, Y \a\t g:i a', strtotime($event['event_date'] ?? 'now')) ?>
                    </div>
                    <div class="card-meta-item">
                        📍 <strong>Venue:</strong> <?= htmlspecialchars($event['venue'] ?? 'TBA') ?>
                    </div>
                    <div class="card-meta-item">
                        🎟️ <strong>Tickets Booked:</strong> <?= $booked ?> <?= $capacity !== null ? "/ {$capacity}" : "" ?>
                        <?php if ($isSoldOut): ?>
                            <span class="badge badge-danger" style="margin-left: 0.5rem;">Sold Out</span>
                        <?php elseif ($capacity !== null && ($capacity - $booked) <= 5): ?>
                            <span class="badge badge-warning" style="margin-left: 0.5rem;">Only <?= ($capacity - $booked) ?> left!</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($event['description'])): ?>
                    <div class="card-body">
                        <?= htmlspecialchars(mb_strimwidth($event['description'], 0, 120, '...')) ?>
                    </div>
                <?php endif; ?>

                <div class="card-footer">
                    <a href="<?= url('events/view/' . $event['id']) ?>" class="btn btn-sm btn-secondary">Details</a>
                    
                    <?php if (!$isSoldOut): ?>
                        <a href="<?= url('tickets/book/' . $event['id']) ?>" class="btn btn-sm btn-primary">Book Tickets</a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed;">Sold Out</button>
                    <?php endif; ?>

                    <?php if ($userRole === 'admin'): ?>
                        <a href="<?= url('events/edit/' . $event['id']) ?>" class="btn btn-sm btn-secondary" style="margin-left: auto;">Edit</a>
                        
                        <form method="POST" action="<?= url('events/delete/' . $event['id']) ?>" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this event? All associated tickets will be deleted.');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <img src="<?= asset('event-pro.png') ?>" alt="EventPro Logo" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 1rem; opacity: 0.85;">
        <h3>No Events Found</h3>
        <p><?= htmlspecialchars($message ?? 'There are currently no upcoming events.') ?></p>
        <?php if ($userRole === 'admin'): ?>
            <a href="<?= url('events/create') ?>" class="btn btn-primary">Create Your First Event</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>

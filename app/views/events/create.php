<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Create Event');
?>

<div class="form-card">
    <h1 class="page-title" style="margin-bottom: 1.5rem;">Create New Event</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('events/create') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="title" class="form-label">Event Title *</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Annual Tech Summit 2026"
                   value="<?= htmlspecialchars($old_input['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Event Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Provide event details, schedule, or highlights..."><?= htmlspecialchars($old_input['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="date" class="form-label">Date & Time *</label>
            <input type="datetime-local" id="date" name="date" class="form-control" required 
                   value="<?= htmlspecialchars($old_input['date'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="venue" class="form-label">Venue / Location *</label>
            <input type="text" id="venue" name="venue" class="form-control" required placeholder="e.g. Grand Convention Center, Hall B"
                   value="<?= htmlspecialchars($old_input['venue'] ?? '') ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="price" class="form-label">Ticket Price ($) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0.01" class="form-control" required placeholder="29.99"
                       value="<?= htmlspecialchars($old_input['price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="capacity" class="form-label">Total Capacity (Max Tickets)</label>
                <input type="number" id="capacity" name="capacity" min="1" class="form-control" placeholder="Leave blank for unlimited"
                       value="<?= htmlspecialchars($old_input['capacity'] ?? '') ?>">
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Create Event</button>
            <a href="<?= url('events') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php renderFooter(); ?>

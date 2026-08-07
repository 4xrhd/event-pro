<?php
require_once APP_ROOT . '/app/views/layout.php';
renderHeader('Edit Event');
?>

<div class="form-card">
    <h1 class="page-title" style="margin-bottom: 1.5rem;">Edit Event</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('events/edit/' . (int) $event['id']) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="title" class="form-label">Event Title *</label>
            <input type="text" id="title" name="title" class="form-control" required 
                   value="<?= htmlspecialchars($event['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Tell attendees about this event..."><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="date" class="form-label">Date & Time *</label>
            <input type="datetime-local" id="date" name="date" class="form-control" required 
                   value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'] ?? 'now')) ?>">
        </div>

        <div class="form-group">
            <label for="venue" class="form-label">Venue / Location *</label>
            <input type="text" id="venue" name="venue" class="form-control" required 
                   value="<?= htmlspecialchars($event['venue'] ?? '') ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="price" class="form-label">Ticket Price ($) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0.01" class="form-control" required 
                       value="<?= htmlspecialchars($event['price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="capacity" class="form-label">Capacity (Max Attendees)</label>
                <input type="number" id="capacity" name="capacity" min="1" class="form-control" placeholder="Unlimited" 
                       value="<?= htmlspecialchars($event['capacity'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Event Status</label>
            <select id="status" name="status" class="form-control">
                <option value="published" <?= ($event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($event['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="completed" <?= ($event['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= ($event['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
            <a href="<?= url('events') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php renderFooter(); ?>

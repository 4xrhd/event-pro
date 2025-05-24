<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - EventPro</title>
    <style>
        .event-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .event-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .create-btn {
            background: #2ecc71;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: background 0.3s;
        }
        .create-btn:hover {
            background: #27ae60;
        }
        .book-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
        .book-btn:hover {
            background: #2980b9;
        }
        .no-events {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .event-date {
            color: #e74c3c;
            font-weight: bold;
        }
        .event-price {
            font-weight: bold;
            color: #2ecc71;
        }
        .delete-btn {
    display: inline-block;
    background-color: #e74c3c;
    color: #fff;
    padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
}

.delete-btn:hover {
    background-color: #c0392b;
}

    </style>
</head>
<body>
<div class="event-container">
    <h1>Upcoming Events</h1>
    <a href="?url=events/create" class="create-btn">Create New Event</a>
    <a href="/index.php?url=auth/logout">Logout</a>
    <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <h2><?= htmlspecialchars($event['title'] ?? 'Untitled Event') ?></h2>
                <p><span class="event-date">Date:</span> <?= date('F j, Y g:i a', strtotime($event['event_date'] ?? '')) ?></p>
                <p><span class="event-date">Venue:</span> <?= htmlspecialchars($event['venue'] ?? 'Location not specified') ?></p>
                <p><span class="event-price">Price:</span> $<?= number_format($event['price'] ?? 0, 2) ?></p>

                <a href="?url=tickets/book/<?= $event['id'] ?? '' ?>" class="book-btn">Book Tickets</a>

                <?php if (!empty($event['id'])): ?>
                    <a href="?url=events/view/<?= $event['id'] ?>" class="book-btn" style="background:#9b59b6;">View Event</a>
                    <a href="?url=events/delete/<?= $event['id'] ?>" class="delete-btn" style="background:#e74c3c;" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-events">
            <h2>No events found</h2>
            <p>There are currently no upcoming events. Check back later or create a new event.</p>
        </div>
    <?php endif; ?>
</div>


</body>
</html>
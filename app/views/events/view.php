<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title']) ?> - Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 30px;
        }

        .event-details-container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .event-info {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .event-info span {
            font-weight: bold;
            color: #444;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .back-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="event-details-container">
    <h1><?= htmlspecialchars($event['title']) ?></h1>

    <div class="event-info">
        <span>Date:</span>
        <?= date('F j, Y \a\t g:i a', strtotime($event['event_date'])) ?>
    </div>

    <div class="event-info">
        <span>Venue:</span>
        <?= htmlspecialchars($event['venue']) ?>
    </div>

    <div class="event-info">
        <span>Price:</span>
        $<?= number_format($event['price'], 2) ?>
    </div>

    <a class="back-btn" href="?url=events">← Back to Events</a>
</div>

</body>
</html>

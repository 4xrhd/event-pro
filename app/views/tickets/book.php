<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets - EventPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .booking-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(0,0,0,0.05);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .event-info {
            background: #eef2f7;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.03);
        }

        .event-info h2 {
            margin: 0 0 10px;
            color: #2c3e50;
        }

        .event-info p {
            margin: 5px 0;
            color: #555;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccd6dd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
        }

        button {
            width: 100%;
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #c0392b;
        }

        @media (max-width: 600px) {
            .booking-container {
                margin: 30px 15px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h1>🎫 Book Tickets</h1>

        <div class="event-info">
            <h2><?= htmlspecialchars($event['title']) ?></h2>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($event['price']) ?> per ticket</p>
        </div>

        <form method="POST" action="index.php?url=/tickets/book/<?= $eventId ?>">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="quantity">Number of Tickets</label>
                <select id="quantity" name="quantity" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <button type="submit">Book Now!</button>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets - EventPro</title>
    <style>
        .booking-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .event-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h1>Book Tickets</h1>
        
        <div class="event-info">
            <h2><?= htmlspecialchars($event['title']) ?></h2>
            <p>Date: <?= htmlspecialchars($event['date']) ?></p>
            <p>Venue: <?= htmlspecialchars($event['venue']) ?></p>
            <p>Price: $<?= htmlspecialchars($event['price']) ?> per ticket</p>
        </div>
        
        <form method="POST" action="/tickets/book">
            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
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
            
            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>
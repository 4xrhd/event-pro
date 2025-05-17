<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<h2>Create a New Event</h2>
<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Date:</label><br>
    <input type="date" name="date" required><br><br>

    <label>Venue:</label><br>
    <input type="text" name="venue" required><br><br>

    <label>Price (USD):</label><br>
    <input type="number" name="price" step="0.01" required><br><br>

    <button type="submit">Create Event</button>
</form>
</body>
</html>

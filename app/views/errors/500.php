<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - EventPro</title>
    <link rel="icon" type="image/png" href="<?= asset('event-pro.png') ?>">
    <link rel="shortcut icon" href="<?= asset('favicon.ico') ?>">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #dc3545; }
    </style>
</head>
<body>
    <img src="<?= asset('event-pro.png') ?>" alt="EventPro Logo" style="height: 48px; width: auto; object-fit: contain; margin-bottom: 1rem;">
    <h1>500 Server Error</h1>
    <p>Sorry, something went wrong on our end.</p>
    <p>Our team has been notified and we're working to fix it.</p>
    <p><a href="<?= url('events') ?>">Return to Home</a></p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<h2>Ticket Confirmed!</h2>
<p><strong>Name:</strong> <?= $name ?></p>
<p><strong>Email:</strong> <?= $email ?></p>
<p><strong>Ticket ID:</strong> <?= $ticketId ?></p>
<p><strong>QR Code:</strong></p>
<img src="<?= $qrPath ?>" alt="Ticket QR">

<a href="/ticket/download/<?= $ticketId ?>" target="_blank">📄 Download PDF Ticket</a>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .confirmation-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #10b981;
            margin-bottom: 1rem;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
        }

        img {
            margin-top: 10px;
            width: 180px;
            height: 180px;
        }

        a.button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        a.button:hover {
            background: #2563eb;
        }

        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        #toast.show {
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="confirmation-box">
    <h2>🎉 Ticket Confirmed!</h2>

    <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    <p><strong>Ticket ID:</strong> <?= $ticketId ?></p>

    <p><strong>QR Code:</strong></p>
    <img src="<?= $qrPath ?>" alt="Ticket QR">

    <br>
    <a class="button" href="index.php?url=tickets/download/<?= $ticketId ?>" target="_blank">📄 Download PDF Ticket</a>
</div>

<div id="toast">✅ Ticket generated successfully!</div>

<script>
    // Show toast on load
    window.onload = () => {
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    };
</script>
</body>
</html>

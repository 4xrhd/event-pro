<?php
$mysqli = new mysqli("localhost", "root", "toor", "eventpro");
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

echo "✅ Connected successfully";

$stmt = $mysqli->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$email = "test@example.com";
$password = password_hash("123456", PASSWORD_BCRYPT);
$stmt->bind_param("ss", $email, $password);

if ($stmt->execute()) {
    echo "✅ Inserted test user";
} else {
    echo "❌ Insert failed";
}



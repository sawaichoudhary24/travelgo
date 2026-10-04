<?php

require_once "../includes/auth.php";
?>
<?php

require_once "../config/database.php";

$name = "TravelGo Admin";
$email = "admin@travelgo.com";
$password = "admin123";

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    INSERT INTO users
    (
        name,
        email,
        password,
        role
    )
    VALUES (?, ?, ?, 'admin')
");

$stmt->execute([
    $name,
    $email,
    $hashedPassword
]);

echo "Admin created successfully!";

echo "<br><br>";

echo "Email: admin@travelgo.com";

echo "<br>";

echo "Password: admin123";
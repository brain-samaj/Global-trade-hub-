$passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (
        full_name,
        email,
        phone,
        password,
        role
    )
    VALUES (?, ?, ?, ?, 'buyer')
");

$stmt->execute([
    $_POST['full_name'],
    $_POST['email'],
    $_POST['phone'],
    $passwordHash
]);

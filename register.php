<?php
require_once 'includes/config.php';

$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $errore = 'Tutti i campi sono obbligatori.';
    } elseif (strlen($password) < 6) {
        $errore = 'La password deve essere di almeno 6 caratteri.';
    } else {
        // Controllo se username o email esistono già
        $stmt = $pdo->prepare('SELECT id FROM Utente WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errore = 'Username o email già registrati.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO Utente (username, email, password_hash, ruolo) VALUES (?, ?, ?, ?)'
            );
            // Il primo utente registrato lo rendiamo admin per comodità di test,
            // tutti gli altri normali. In produzione andrebbe gestito diversamente.
            $stmt->execute([$username, $email, $hash, 'utente']);

            header('Location: login.php?registrato=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-page">
        <h1>Registrazione</h1>

        <?php if ($errore): ?>
            <p style="color: red;"><?= htmlspecialchars($errore) ?></p>
        <?php endif; ?>

        <form method="post" action="register.php" class="auth-form">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Password: <input type="password" name="password" required></label><br>
            <button type="submit">Registrati</button>
        </form>

        <p>Hai già un account? <a href="login.php">Accedi</a></p>
    </div>
</body>
</html>

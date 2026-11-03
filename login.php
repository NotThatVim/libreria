<?php
require_once 'includes/config.php';

$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash, ruolo FROM Utente WHERE username = ?');
    $stmt->execute([$username]);
    $utente = $stmt->fetch();

    if ($utente && password_verify($password, $utente['password_hash'])) {
        $_SESSION['utente_id'] = $utente['id'];
        $_SESSION['username']  = $utente['username'];
        $_SESSION['ruolo']     = $utente['ruolo'];

        header('Location: mia_libreria.php');
        exit;
    } else {
        $errore = 'Username o password errati.';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-page">
        <h1>Login</h1>

        <?php if (isset($_GET['registrato'])): ?>
            <p style="color: green;">Registrazione completata, ora puoi accedere.</p>
        <?php endif; ?>

        <?php if ($errore): ?>
            <p style="color: red;"><?= htmlspecialchars($errore) ?></p>
        <?php endif; ?>

        <form method="post" action="login.php" class="auth-form">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Password: <input type="password" name="password" required></label><br>
            <button type="submit">Accedi</button>
        </form>

        <p>Non hai un account? <a href="register.php">Registrati</a></p>
    </div>
</body>
</html>

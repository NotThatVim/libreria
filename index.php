<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Libreria - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <h1>Benvenuto, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>Ruolo: <?= htmlspecialchars($_SESSION['ruolo']) ?></p>
    <p><a href="catalogo.php"><button type="button">Vai al catalogo</button></a></p>
</body>
</html>

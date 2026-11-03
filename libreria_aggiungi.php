<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['opera_id'])) {
    header('Location: catalogo.php');
    exit;
}

$utente_id = $_SESSION['utente_id'];
$opera_id  = (int) $_POST['opera_id'];

// Controlla se è già in libreria (la PK composita (utente_id, opera_id) lo impedirebbe comunque,
// ma controlliamo prima per dare un messaggio più chiaro invece di un errore SQL)
$stmt = $pdo->prepare('SELECT 1 FROM Libreria WHERE utente_id = ? AND opera_id = ?');
$stmt->execute([$utente_id, $opera_id]);

if ($stmt->fetch()) {
    header('Location: mia_libreria.php?gia_presente=1');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO Libreria (utente_id, opera_id, stato, capitoli_letti) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$utente_id, $opera_id, 'da_leggere', 0]);

header('Location: mia_libreria.php?aggiunto=1');
exit;

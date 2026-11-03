<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['ruolo'] !== 'admin') {
    die('Accesso negato: questa pagina è riservata agli amministratori.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['opera_id'])) {
    header('Location: admin_opere.php');
    exit;
}

$opera_id = (int) $_POST['opera_id'];

// Grazie a ON DELETE CASCADE su Opera_Categoria e Libreria,
// cancellando l'opera si ripuliscono automaticamente anche le associazioni
// (categorie collegate e presenza nelle librerie personali degli utenti)
$stmt = $pdo->prepare('DELETE FROM Opera WHERE id = ?');
$stmt->execute([$opera_id]);

header('Location: admin_opere.php?eliminato=1');
exit;

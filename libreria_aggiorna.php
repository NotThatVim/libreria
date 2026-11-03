<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mia_libreria.php');
    exit;
}

$utente_id      = $_SESSION['utente_id'];
$opera_id       = (int) $_POST['opera_id'];
$stato          = $_POST['stato'] ?? 'da_leggere';
$capitoli_letti = (int) $_POST['capitoli_letti'];
$valutazione    = $_POST['valutazione'] !== '' ? (int) $_POST['valutazione'] : null;

$stati_validi = ['da_leggere', 'in_corso', 'completato', 'abbandonato', 'interrotto'];
if (!in_array($stato, $stati_validi, true)) {
    header('Location: mia_libreria.php?errore=Stato non valido');
    exit;
}

try {
    $stmt = $pdo->prepare(
        'UPDATE Libreria
         SET stato = ?, capitoli_letti = ?, valutazione = ?
         WHERE utente_id = ? AND opera_id = ?'
    );
    $stmt->execute([$stato, $capitoli_letti, $valutazione, $utente_id, $opera_id]);

    header('Location: mia_libreria.php?aggiornato=1');
    exit;
} catch (PDOException $e) {
    // Il trigger sul DB blocca capitoli_letti > capitoli_totali con SIGNALSQLSTATE 45000
    // qui intercettiamo l'errore e lo mostriamo in modo leggibile invece di un crash
    $messaggio = str_contains($e->getMessage(), 'capitoli_letti')
        ? 'Numero di capitoli letti superiore al totale dell\'opera.'
        : 'Errore durante l\'aggiornamento.';

    header('Location: mia_libreria.php?errore=' . urlencode($messaggio));
    exit;
}

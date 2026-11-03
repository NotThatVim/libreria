<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}

$utente_id = $_SESSION['utente_id'];

// --- Totali generali e percentuale di completamento ---
$stmt = $pdo->prepare('
    SELECT COUNT(*) AS totale,
           SUM(CASE WHEN stato = "completato" THEN 1 ELSE 0 END) AS completate,
           ROUND(AVG(valutazione), 2) AS voto_medio
    FROM Libreria
    WHERE utente_id = ?
');
$stmt->execute([$utente_id]);
$totali = $stmt->fetch();

$percentuale = $totali['totale'] > 0
    ? round($totali['completate'] * 100 / $totali['totale'], 1)
    : 0;

// --- Distribuzione per stato ---
$stmt = $pdo->prepare('
    SELECT stato, COUNT(*) AS conteggio
    FROM Libreria
    WHERE utente_id = ?
    GROUP BY stato
');
$stmt->execute([$utente_id]);
$per_stato = $stmt->fetchAll();

// --- Distribuzione per categoria (generi preferiti) ---
$stmt = $pdo->prepare('
    SELECT c.nome, COUNT(*) AS conteggio
    FROM Libreria l
    JOIN Opera_Categoria oc ON l.opera_id = oc.opera_id
    JOIN Categoria c ON oc.categoria_id = c.id
    WHERE l.utente_id = ?
    GROUP BY c.id, c.nome
    ORDER BY conteggio DESC
');
$stmt->execute([$utente_id]);
$per_categoria = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le mie statistiche</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <div class="page-content">
    <h1>Le mie statistiche</h1>

    <?php if ($totali['totale'] == 0): ?>
        <p>Non hai ancora opere nella tua libreria — aggiungine qualcuna dal <a href="catalogo.php">catalogo</a> per vedere le statistiche.</p>
    <?php else: ?>

        <h2>Riepilogo</h2>
        <ul>
            <li>Opere totali in libreria: <strong><?= $totali['totale'] ?></strong></li>
            <li>Opere completate: <strong><?= $totali['completate'] ?></strong></li>
            <li>Percentuale di completamento: <strong><?= $percentuale ?>%</strong></li>
            <li>Voto medio dato: <strong><?= $totali['voto_medio'] ?? 'nessun voto ancora' ?></strong></li>
        </ul>

        <h2>Distribuzione per stato</h2>
        <table>
            <tr><th>Stato</th><th>Numero opere</th></tr>
            <?php foreach ($per_stato as $s): ?>
                <tr>
                    <td><?= ucfirst(str_replace('_', ' ', $s['stato'])) ?></td>
                    <td><?= $s['conteggio'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Generi più letti</h2>
        <table>
            <tr><th>Categoria</th><th>Numero opere</th></tr>
            <?php foreach ($per_categoria as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nome']) ?></td>
                    <td><?= $c['conteggio'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>
    </div>
</body>
</html>

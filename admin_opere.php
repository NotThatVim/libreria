<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['ruolo'] !== 'admin') {
    die('Accesso negato: questa pagina è riservata agli amministratori.');
}

$opere = $pdo->query('
    SELECT o.id, o.titolo, o.tipo, o.capitoli_totali, pa.nome AS autore_nome
    FROM Opera o
    JOIN Persona pa ON o.autore_id = pa.id
    ORDER BY o.titolo
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestione Opere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <div class="page-content">
    <h1>Gestione Opere (Admin)</h1>

    <?php if (isset($_GET['salvato'])): ?>
        <p style="color: green;">Opera salvata correttamente.</p>
    <?php endif; ?>
    <?php if (isset($_GET['eliminato'])): ?>
        <p style="color: green;">Opera eliminata.</p>
    <?php endif; ?>
    <?php if (isset($_GET['errore'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_GET['errore']) ?></p>
    <?php endif; ?>

    <p><a href="admin_opera_form.php">+ Aggiungi nuova opera</a></p>

    <table>
        <tr>
            <th>Titolo</th>
            <th>Tipo</th>
            <th>Autore</th>
            <th>Capitoli</th>
            <th>Azioni</th>
        </tr>
        <?php foreach ($opere as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['titolo']) ?></td>
            <td><?= htmlspecialchars($o['tipo']) ?></td>
            <td><?= htmlspecialchars($o['autore_nome']) ?></td>
            <td><?= $o['capitoli_totali'] ?></td>
            <td>
                <a href="admin_opera_form.php?id=<?= $o['id'] ?>">Modifica</a>
                <form method="post" action="admin_opera_elimina.php" style="display:inline;"
                      onsubmit="return confirm('Eliminare questa opera? Verrà rimossa anche dalle librerie personali degli utenti.');">
                    <input type="hidden" name="opera_id" value="<?= $o['id'] ?>">
                    <button type="submit">Elimina</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
</body>
</html>

<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['ruolo'] !== 'admin') {
    die('Accesso negato: questa pagina è riservata agli amministratori.');
}

$opera_id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$modifica = $opera_id !== null;
$errore = '';

// Dati necessari per le select del form
$persone   = $pdo->query('SELECT id, nome FROM Persona ORDER BY nome')->fetchAll();
$categorie = $pdo->query('SELECT id, nome FROM Categoria ORDER BY nome')->fetchAll();

// Valori di default (per aggiunta) o caricati dal DB (per modifica)
$dati = [
    'titolo' => '',
    'tipo' => 'manga',
    'autore_id' => '',
    'disegnatore_id' => '',
    'protagonista_nome' => '',
    'protagonista_nazionalita' => '',
    'capitoli_totali' => 0,
];
$categorie_selezionate = [];

if ($modifica) {
    $stmt = $pdo->prepare('SELECT * FROM Opera WHERE id = ?');
    $stmt->execute([$opera_id]);
    $opera = $stmt->fetch();

    if (!$opera) {
        die('Opera non trovata.');
    }
    $dati = $opera;

    $stmt = $pdo->prepare('SELECT categoria_id FROM Opera_Categoria WHERE opera_id = ?');
    $stmt->execute([$opera_id]);
    $categorie_selezionate = array_column($stmt->fetchAll(), 'categoria_id');
}

// --- Gestione submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dati['titolo'] = trim($_POST['titolo'] ?? '');
    $dati['tipo'] = $_POST['tipo'] ?? 'manga';
    $dati['autore_id'] = $_POST['autore_id'] ?? '';
    $dati['disegnatore_id'] = $_POST['disegnatore_id'] !== '' ? $_POST['disegnatore_id'] : null;
    $dati['protagonista_nome'] = trim($_POST['protagonista_nome'] ?? '');
    $dati['protagonista_nazionalita'] = trim($_POST['protagonista_nazionalita'] ?? '');
    $dati['capitoli_totali'] = (int) ($_POST['capitoli_totali'] ?? 0);
    $categorie_post = $_POST['categorie'] ?? [];

    $autore_nuovo_nome = trim($_POST['autore_nuovo_nome'] ?? '');
    $autore_nuovo_nazionalita = trim($_POST['autore_nuovo_nazionalita'] ?? '');
    $disegnatore_nuovo_nome = trim($_POST['disegnatore_nuovo_nome'] ?? '');
    $disegnatore_nuovo_nazionalita = trim($_POST['disegnatore_nuovo_nazionalita'] ?? '');

    // Se è stata scelta l'opzione "nuovo", il nome è obbligatorio per poter creare la Persona
    if ($dati['autore_id'] === 'nuovo' && $autore_nuovo_nome === '') {
        $errore = 'Inserisci il nome del nuovo autore.';
    } elseif ($dati['disegnatore_id'] === 'nuovo' && $disegnatore_nuovo_nome === '') {
        $errore = 'Inserisci il nome del nuovo disegnatore.';
    } elseif ($dati['titolo'] === '' || $dati['autore_id'] === '') {
        $errore = 'Titolo e autore sono obbligatori.';
    } elseif ($dati['tipo'] !== 'novel' && $dati['disegnatore_id'] === null) {
        $errore = 'Il disegnatore è obbligatorio per manga e manhwa.';
    } elseif ($dati['tipo'] === 'novel' && $dati['disegnatore_id'] !== null) {
        $errore = 'Le novel non devono avere un disegnatore.';
    } else {
        try {
            // Se l'utente ha scelto "+ Nuovo autore/disegnatore", crea prima la Persona
            // e usa l'id appena generato al posto del valore 'nuovo' nella riga di Opera.
            if ($dati['autore_id'] === 'nuovo') {
                $stmt = $pdo->prepare('INSERT INTO Persona (nome, nazionalita) VALUES (?, ?)');
                $stmt->execute([$autore_nuovo_nome, $autore_nuovo_nazionalita ?: null]);
                $dati['autore_id'] = $pdo->lastInsertId();
            }
            if ($dati['disegnatore_id'] === 'nuovo') {
                $stmt = $pdo->prepare('INSERT INTO Persona (nome, nazionalita) VALUES (?, ?)');
                $stmt->execute([$disegnatore_nuovo_nome, $disegnatore_nuovo_nazionalita ?: null]);
                $dati['disegnatore_id'] = $pdo->lastInsertId();
            }

            if ($modifica) {
                $stmt = $pdo->prepare(
                    'UPDATE Opera SET titolo=?, tipo=?, autore_id=?, disegnatore_id=?,
                     protagonista_nome=?, protagonista_nazionalita=?, capitoli_totali=?
                     WHERE id=?'
                );
                $stmt->execute([
                    $dati['titolo'], $dati['tipo'], $dati['autore_id'], $dati['disegnatore_id'],
                    $dati['protagonista_nome'], $dati['protagonista_nazionalita'], $dati['capitoli_totali'],
                    $opera_id,
                ]);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO Opera (titolo, tipo, autore_id, disegnatore_id,
                     protagonista_nome, protagonista_nazionalita, capitoli_totali)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $dati['titolo'], $dati['tipo'], $dati['autore_id'], $dati['disegnatore_id'],
                    $dati['protagonista_nome'], $dati['protagonista_nazionalita'], $dati['capitoli_totali'],
                ]);
                $opera_id = $pdo->lastInsertId();
            }

            // Risincronizza le categorie: cancella le vecchie associazioni e reinserisce quelle selezionate.
            // Più semplice ed affidabile che calcolare un diff riga per riga.
            $pdo->prepare('DELETE FROM Opera_Categoria WHERE opera_id = ?')->execute([$opera_id]);

            if (!empty($categorie_post)) {
                $stmt = $pdo->prepare('INSERT INTO Opera_Categoria (opera_id, categoria_id) VALUES (?, ?)');
                foreach ($categorie_post as $cat_id) {
                    $stmt->execute([$opera_id, (int) $cat_id]);
                }
            }

            header('Location: admin_opere.php?salvato=1');
            exit;
        } catch (PDOException $e) {
            $errore = 'Errore nel salvataggio: ' . $e->getMessage();
        }
    }

    // Se c'è errore, ricarica le categorie selezionate dal POST per non perderle nel form
    $categorie_selezionate = array_map('intval', $categorie_post ?? []);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?= $modifica ? 'Modifica' : 'Aggiungi' ?> Opera</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <div class="form-page">
    <h1><?= $modifica ? 'Modifica' : 'Aggiungi' ?> Opera</h1>
    <p><a href="admin_opere.php">Torna alla gestione opere</a></p>

    <?php if ($errore): ?>
        <p style="color: red;"><?= htmlspecialchars($errore) ?></p>
    <?php endif; ?>

    <form method="post" action="admin_opera_form.php<?= $modifica ? '?id=' . $opera_id : '' ?>">
        <label>Titolo: <input type="text" name="titolo" value="<?= htmlspecialchars($dati['titolo']) ?>" required></label><br><br>

        <label>Tipo:
            <select name="tipo" id="tipo">
                <?php foreach (['manga', 'manhwa', 'novel'] as $t): ?>
                    <option value="<?= $t ?>" <?= $dati['tipo'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Autore:
            <select name="autore_id" id="autore_id" required>
                <option value="">-- seleziona --</option>
                <option value="nuovo" <?= $dati['autore_id'] === 'nuovo' ? 'selected' : '' ?>>+ Nuovo autore...</option>
                <?php foreach ($persone as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $dati['autore_id'] == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div id="autore_nuovo_campi" style="display: none; margin: 0.5rem 0 1rem; padding-left: 1rem; border-left: 2px solid var(--border);">
            <label>Nome nuovo autore: <input type="text" name="autore_nuovo_nome" value="<?= htmlspecialchars($_POST['autore_nuovo_nome'] ?? '') ?>"></label>
            <label>Nazionalità: <input type="text" name="autore_nuovo_nazionalita" value="<?= htmlspecialchars($_POST['autore_nuovo_nazionalita'] ?? '') ?>"></label>
        </div>
        <br>

        <label>Disegnatore (lascia vuoto se novel):
            <select name="disegnatore_id" id="disegnatore_id">
                <option value="">-- nessuno --</option>
                <option value="nuovo" <?= $dati['disegnatore_id'] === 'nuovo' ? 'selected' : '' ?>>+ Nuovo disegnatore...</option>
                <?php foreach ($persone as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $dati['disegnatore_id'] == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div id="disegnatore_nuovo_campi" style="display: none; margin: 0.5rem 0 1rem; padding-left: 1rem; border-left: 2px solid var(--border);">
            <label>Nome nuovo disegnatore: <input type="text" name="disegnatore_nuovo_nome" value="<?= htmlspecialchars($_POST['disegnatore_nuovo_nome'] ?? '') ?>"></label>
            <label>Nazionalità: <input type="text" name="disegnatore_nuovo_nazionalita" value="<?= htmlspecialchars($_POST['disegnatore_nuovo_nazionalita'] ?? '') ?>"></label>
        </div>
        <br>

        <label>Nome protagonista: <input type="text" name="protagonista_nome" value="<?= htmlspecialchars($dati['protagonista_nome'] ?? '') ?>"></label><br><br>

        <label>Nazionalità protagonista: <input type="text" name="protagonista_nazionalita" value="<?= htmlspecialchars($dati['protagonista_nazionalita'] ?? '') ?>"></label><br><br>

        <label>Capitoli totali: <input type="number" name="capitoli_totali" value="<?= $dati['capitoli_totali'] ?>" min="0" required></label><br><br>

        <fieldset>
            <legend>Categorie</legend>
            <?php foreach ($categorie as $c): ?>
                <label>
                    <input type="checkbox" name="categorie[]" value="<?= $c['id'] ?>"
                        <?= in_array($c['id'], $categorie_selezionate) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($c['nome']) ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

        <button type="submit"><?= $modifica ? 'Salva modifiche' : 'Crea opera' ?></button>
    </form>

    <script>
        // Piccolo aiuto UX lato client: svuota il disegnatore se si seleziona 'novel'
        // Il controllo VERO che impedisce dati incoerenti resta lato server + vincolo DB.
        document.getElementById('tipo').addEventListener('change', function () {
            if (this.value === 'novel') {
                document.getElementById('disegnatore_id').value = '';
                document.getElementById('disegnatore_nuovo_campi').style.display = 'none';
            }
        });

        // Mostra/nasconde i campi "nome nuovo autore/disegnatore" in base alla scelta nella select
        function collegaCampoNuovo(selectId, campiId) {
            const select = document.getElementById(selectId);
            const campi = document.getElementById(campiId);

            function aggiorna() {
                campi.style.display = select.value === 'nuovo' ? 'block' : 'none';
            }

            select.addEventListener('change', aggiorna);
            aggiorna(); // stato iniziale, utile se si ricarica la pagina con 'nuovo' già selezionato dopo un errore
        }

        collegaCampoNuovo('autore_id', 'autore_nuovo_campi');
        collegaCampoNuovo('disegnatore_id', 'disegnatore_nuovo_campi');
    </script>
    </div>
</body>
</html>

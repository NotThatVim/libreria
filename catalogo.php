<?php
require_once 'includes/config.php';

if (!isset($_SESSION['utente_id'])) {
    header('Location: login.php');
    exit;
}

// --- Costruzione filtri dinamici ---
$categoria_id = $_GET['categoria'] ?? '';
$autore_id    = $_GET['autore'] ?? '';
$disegnatore_id = $_GET['disegnatore'] ?? '';
$nazionalita  = trim($_GET['nazionalita'] ?? '');

$where = [];
$params = [];

if ($categoria_id !== '') {
    $where[] = 'oc.categoria_id = ?';
    $params[] = $categoria_id;
}
if ($autore_id !== '') {
    $where[] = 'o.autore_id = ?';
    $params[] = $autore_id;
}
if ($disegnatore_id !== '') {
    $where[] = 'o.disegnatore_id = ?';
    $params[] = $disegnatore_id;
}
if ($nazionalita !== '') {
    $where[] = 'o.protagonista_nazionalita LIKE ?';
    $params[] = '%' . $nazionalita . '%';
}

// --- Ordinamento ---
$ordine = $_GET['ordine'] ?? 'titolo';
$ordini_validi = [
    'titolo'     => 'o.titolo ASC',
    'popolarita' => 'numero_lettori DESC, o.titolo ASC',
    'voto'       => 'voto_medio DESC, o.titolo ASC',
];
$order_by = $ordini_validi[$ordine] ?? $ordini_validi['titolo'];

$sql = '
    SELECT DISTINCT o.id, o.titolo, o.tipo, o.capitoli_totali,
           o.protagonista_nome, o.protagonista_nazionalita,
           pa.nome AS autore_nome, pd.nome AS disegnatore_nome,
           (SELECT COUNT(DISTINCT l.utente_id) FROM Libreria l WHERE l.opera_id = o.id) AS numero_lettori,
           (SELECT ROUND(AVG(l.valutazione), 1) FROM Libreria l WHERE l.opera_id = o.id AND l.valutazione IS NOT NULL) AS voto_medio
    FROM Opera o
    JOIN Persona pa ON o.autore_id = pa.id
    LEFT JOIN Persona pd ON o.disegnatore_id = pd.id
    LEFT JOIN Opera_Categoria oc ON o.id = oc.opera_id
';

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . $order_by;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$opere = $stmt->fetchAll();

// Dati per popolare le select dei filtri.
// Nota: per Autore/Disegnatore non usiamo l'intera tabella Persona,
// ma solo chi compare effettivamente in quel ruolo su almeno un'opera
// (es. un autore di sole novel non deve comparire nella tendina Disegnatore).
$categorie = $pdo->query('SELECT id, nome FROM Categoria ORDER BY nome')->fetchAll();

$autori = $pdo->query('
    SELECT DISTINCT p.id, p.nome
    FROM Persona p
    JOIN Opera o ON o.autore_id = p.id
    ORDER BY p.nome
')->fetchAll();

$disegnatori = $pdo->query('
    SELECT DISTINCT p.id, p.nome
    FROM Persona p
    JOIN Opera o ON o.disegnatore_id = p.id
    ORDER BY p.nome
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Catalogo Opere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <div class="page-content">
        <h1>Catalogo Opere</h1>

        <form method="get" action="catalogo.php" class="filter-form">
        <label>Categoria:
            <select name="categoria">
                <option value="">Tutte</option>
                <?php foreach ($categorie as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $categoria_id == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Autore:
            <select name="autore">
                <option value="">Tutti</option>
                <?php foreach ($autori as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $autore_id == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Disegnatore:
            <select name="disegnatore">
                <option value="">Tutti</option>
                <?php foreach ($disegnatori as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $disegnatore_id == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Nazionalità protagonista:
            <input type="text" name="nazionalita" value="<?= htmlspecialchars($nazionalita) ?>">
        </label>

        <label>Ordina per:
            <select name="ordine">
                <option value="titolo" <?= $ordine === 'titolo' ? 'selected' : '' ?>>Titolo (A-Z)</option>
                <option value="popolarita" <?= $ordine === 'popolarita' ? 'selected' : '' ?>>Popolarità (più letta)</option>
                <option value="voto" <?= $ordine === 'voto' ? 'selected' : '' ?>>Voto medio (più alto)</option>
            </select>
        </label>

        <button type="submit">Filtra</button>
        <a href="catalogo.php">Reset</a>
    </form>

    <table class="catalog-table">
        <tr>
            <th>Titolo</th>
            <th>Tipo</th>
            <th>Autore</th>
            <th>Disegnatore</th>
            <th>Protagonista</th>
            <th>Nazionalità</th>
            <th>Capitoli totali</th>
            <th>Lettori</th>
            <th>Voto medio</th>
            <th>Azioni</th>
        </tr>
        <?php foreach ($opere as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['titolo']) ?></td>
            <td><?= htmlspecialchars($o['tipo']) ?></td>
            <td><?= htmlspecialchars($o['autore_nome']) ?></td>
            <td><?= htmlspecialchars($o['disegnatore_nome'] ?? '-') ?></td>
            <td><?= htmlspecialchars($o['protagonista_nome'] ?? '-') ?></td>
            <td><?= htmlspecialchars($o['protagonista_nazionalita'] ?? '-') ?></td>
            <td><?= $o['capitoli_totali'] ?></td>
            <td><?= $o['numero_lettori'] ?></td>
            <td><?= $o['voto_medio'] ?? '-' ?></td>
            <td>
                <form method="post" action="libreria_aggiungi.php" style="display:inline;">
                    <input type="hidden" name="opera_id" value="<?= $o['id'] ?>">
                    <button type="submit">Aggiungi alla mia libreria</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if (empty($opere)): ?>
        <p>Nessuna opera trovata con questi filtri.</p>
    <?php endif; ?>
    </div>
</body>
</html>

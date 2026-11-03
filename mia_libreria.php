<?php
require_once "includes/config.php";

if (!isset($_SESSION["utente_id"])) {
    header("Location: login.php");
    exit();
}

$utente_id = $_SESSION["utente_id"];

// Filtri opzionali
$filtro_stato = $_GET["stato"] ?? "";
$filtro_capitoli = $_GET["capitoli_letti"] ?? "";
$filtro_voto = $_GET["voto_min"] ?? "";

$sql = '
    SELECT l.opera_id, l.stato, l.capitoli_letti, l.valutazione,
           o.titolo, o.tipo, o.capitoli_totali
    FROM Libreria l
    JOIN Opera o ON l.opera_id = o.id
    WHERE l.utente_id = ?
';

$params = [$utente_id];

// Filtro per stato
if ($filtro_stato !== "") {
    $sql .= " AND l.stato = ?";
    $params[] = $filtro_stato;
}

// Filtro per numero minimo di capitoli letti
if ($filtro_capitoli !== "" && is_numeric($filtro_capitoli)) {
    $sql .= " AND l.capitoli_letti >= ?";
    $params[] = (int) $filtro_capitoli;
}

// Filtro per voto minimo
if ($filtro_voto !== "" && is_numeric($filtro_voto)) {
    $sql .= " AND l.valutazione >= ?";
    $params[] = (int) $filtro_voto;
}

$sql .= " ORDER BY o.titolo";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$libreria = $stmt->fetchAll();

$stati_possibili = [
    "da_leggere",
    "in_corso",
    "completato",
    "abbandonato",
    "interrotto",
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>La mia libreria</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once "includes/header.php"; ?>
    <div class="page-content">
    <h1>La mia libreria</h1>

    <?php if (isset($_GET["aggiunto"])): ?>
        <p style="color: green;">Opera aggiunta alla libreria.</p>
    <?php endif; ?>
    <?php if (isset($_GET["gia_presente"])): ?>
        <p style="color: orange;">Questa opera è già nella tua libreria.</p>
    <?php endif; ?>
    <?php if (isset($_GET["aggiornato"])): ?>
        <p style="color: green;">Libreria aggiornata.</p>
    <?php endif; ?>
    <?php if (isset($_GET["errore"])): ?>
        <p style="color: red;"><?= htmlspecialchars($_GET["errore"]) ?></p>
    <?php endif; ?>

    <form method="get" action="mia_libreria.php" class="filter-form">

        <!-- Filtro per stato -->
        <label>Filtra per stato:
            <select name="stato">
                <option value="">Tutti</option>

                <?php foreach ($stati_possibili as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>"
                        <?= $filtro_stato === $s ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $s)) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </label>

        <!-- Filtro per capitoli letti -->
        <label>Capitoli letti almeno:
            <input
                type="number"
                name="capitoli_letti"
                min="0"
                placeholder="Es. 10"
                value="<?= htmlspecialchars($filtro_capitoli) ?>"
                style="width: 80px;"
            >
        </label>

        <!-- Filtro per voto minimo -->
        <label>Voto minimo:
            <select name="voto_min">
                <option value="">Tutti</option>

                <?php for ($v = 1; $v <= 10; $v++): ?>
                    <option value="<?= $v ?>"
                        <?= (string)$filtro_voto === (string)$v ? 'selected' : '' ?>>
                        <?= $v ?>
                    </option>
                <?php endfor; ?>

            </select>
        </label>

        <button type="submit">Filtra</button>

        <a href="mia_libreria.php">Reset filtri</a>

    </form>


    <table>
        <tr>
            <th>Titolo</th>
            <th>Tipo</th>
            <th>Stato</th>
            <th>Capitoli letti / totali</th>
            <th>Valutazione</th>
            <th>Azioni</th>
        </tr>
        <?php foreach ($libreria as $l):
            $fid = "form_" . $l["opera_id"]; ?>
        <form id="<?= $fid ?>" method="post" action="libreria_aggiorna.php"></form>
        <tr>
                <td><?= htmlspecialchars($l["titolo"]) ?></td>
                <td><?= htmlspecialchars($l["tipo"]) ?></td>
                <td>
                    <input type="hidden" name="opera_id" value="<?= $l["opera_id"] ?>" form="<?= $fid ?>">
                    <select name="stato" form="<?= $fid ?>"
                            onchange="if (this.value === 'completato') { document.getElementById('capitoli_<?= $l["opera_id"] ?>').value = <?= $l["capitoli_totali"] ?>; }">
                        <?php foreach ($stati_possibili as $s): ?>
                            <option value="<?= $s ?>" <?= $l["stato"] === $s ? "selected" : "" ?>>
                                <?= ucfirst(str_replace("_", " ", $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="capitoli_letti" id="capitoli_<?= $l["opera_id"] ?>"
                           value="<?= $l["capitoli_letti"] ?>"
                           min="0" max="<?= $l["capitoli_totali"] ?>" style="width: 60px;" form="<?= $fid ?>">
                    / <?= $l["capitoli_totali"] ?>
                </td>
                <td>
                    <select name="valutazione" form="<?= $fid ?>">
                        <option value="">-</option>
                        <?php for ($v = 1; $v <= 10; $v++): ?>
                            <option value="<?= $v ?>" <?= $l["valutazione"] == $v ? "selected" : "" ?>>
                                <?= $v ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" form="<?= $fid ?>">Aggiorna</button>
                </td>
        </tr>
        <?php
        endforeach; ?>
    </table>

    <?php if (empty($libreria)): ?>
        <p>Nessuna opera nella libreria attualmente.</p>
    <?php endif; ?>

    <p>Per aggiungere opere alla tua libreria vai al <a href="catalogo.php">catalogo</a>!</p>
    </div>
</body>
</html>

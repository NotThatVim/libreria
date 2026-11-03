<nav>
    <a href="catalogo.php">Catalogo</a> |
    <a href="mia_libreria.php">La mia libreria</a> |
    <a href="statistiche.php">Statistiche</a> |
    <?php if (($_SESSION['ruolo'] ?? '') === 'admin'): ?>
        | <a href="admin_opere.php">Gestione opere (admin)</a>
    <?php endif; ?>
    | <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username'] ?? '') ?>)</a>
</nav>
<hr>

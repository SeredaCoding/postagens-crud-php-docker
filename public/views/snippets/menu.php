<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="home.php">Blog Dev</a>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
            <span class="navbar-text me-auto">Admin</span>
        <?php endif; ?>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger">Sair <i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
</nav>
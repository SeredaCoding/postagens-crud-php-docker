<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Sessão não existe, redireciona para o login
    header('Location: index.php');
    exit;
}
require_once(__DIR__.'/snippets/header.html');
?>
<body class="d-flex flex-column vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Blog Dev</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-danger">Sair <i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h1 class="mb-4">Bem-vindo ao Blog, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>!</h1>
                            <p class="lead">
                                Este é o seu espaço para compartilhar ideias, artigos e notícias.
                                Abaixo, você pode começar a criar novas postagens ou navegar pelo conteúdo existente.
                            </p>
                            <a href="criar_post.php" class="btn btn-primary me-2">
                                <i class="fa-solid fa-pen-to-square"></i> Criar nova postagem
                            </a>
                            <a href="posts.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-book-open"></i> Ver postagens
                            </a>
                            <hr>
                            <!-- Exemplo de postagens estáticas -->
                            <h3>Últimas postagens</h3>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5>Título da postagem 1</h5>
                                    <small class="text-muted">Publicado em 20/05/2025</small>
                                    <p>Resumo breve da postagem para mostrar como ficará o blog.</p>
                                </li>
                                <li class="list-group-item">
                                    <h5>Título da postagem 2</h5>
                                    <small class="text-muted">Publicado em 18/05/2025</small>
                                    <p>Outro resumo breve para mostrar o estilo do conteúdo.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-light text-center py-3 mt-auto shadow-sm">
        <script>
            const anoAtual = new Date().getFullYear();
            const anoMaisRecente = 2025;
            if (anoAtual == anoMaisRecente) {
                document.write(`&copy; ${anoAtual} Blog Dev. Todos os direitos reservados.`);
            } else {
                document.write(`&copy; ${anoMaisRecente} - ${anoAtual} Blog Dev. Todos os direitos reservados.`);
            }
        </script>
    </footer>
</body>
</html>
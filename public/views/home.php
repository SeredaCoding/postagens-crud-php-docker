<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Sessão não existe, redireciona para o login
    header('Location: index.php');
    exit;
}
require_once(__DIR__.'/snippets/header.html');

?>
<body class="d-flex flex-column min-vh-100">
    <?php require_once(__DIR__.'/snippets/menu.php'); ?>
    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h1 class="mb-4">Bem-vindo ao Blog Dev, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>!</h1>
                            <p class="lead">
                                Este é o seu espaço para compartilhar ideias, artigos e notícias.
                                Abaixo, você pode começar a criar novas postagens ou navegar pelo conteúdo existente.
                            </p>
                            <a href="criar_post.php" class="btn btn-primary me-2 mb-2 mb-sm-0">
                                <i class="fa-solid fa-pen-to-square"></i> Criar nova postagem
                            </a>
                            <a href="posts.php" class="btn btn-outline-secondary mb-2 mb-sm-0">
                                <i class="fa-solid fa-book-open"></i> Ver postagens
                            </a>
                            <hr>
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div id="meus-posts" class="mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once(__DIR__.'/snippets/footer.html'); ?>
</body>
<script>
$(document).ready(function () {
    $.ajax({
        url: urlBase + "/api/posts/meus",
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#loader").addClass("d-none"); // Oculta o loader
            if (response.status === "success" && response.data) {
                let posts = Array.isArray(response.data) ? response.data : [response.data];
                if(posts.length > 0){
                    let html = `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="mb-0">Minhas últimas postagens</h4>
                            <a href="meus_posts.php" class="btn btn-sm btn-secondary">
                                Ver tudo <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                        <ul class="list-group">`;
                    posts.forEach(post => {
                        html += `<li class="list-group-item">
                                    <a href="post.php?id=${post.id}" style="text-decoration: none; color: inherit;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">${post.titulo}</h5>
                                            <small class="text-muted ms-3">Publicado em ${new Date(post.criado_em).toLocaleDateString()}</small>
                                        </div>
                                        <p>${post.conteudo}</p>
                                    </a>
                                </li>`;
                    });
                    html += '</ul>';
                    $("#meus-posts").html(html);
                } else {
                    $("#meus-posts").html('<div class="alert alert-info">Nenhuma postagem sua encontrada.</div>');
                }
            } else {
                $("#meus-posts").html('<div class="alert alert-info">' + (response.message || 'Nenhuma postagem encontrada.') + '</div>');
            }
        },
        error: function () {
            $("#loader").addClass("d-none"); // Oculta o loader mesmo em caso de erro
            $("#meus-posts").html('<div class="alert alert-danger">Erro ao carregar postagens.</div>');
        }
    });
});
</script>
</html>
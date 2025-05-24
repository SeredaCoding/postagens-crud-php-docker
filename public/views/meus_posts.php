<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once(__DIR__.'/snippets/header.html');
?>
<body class="d-flex flex-column vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">Blog Dev</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-danger">Sair <i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-body">
                            <h1 class="mb-4">Minhas Postagens</h1>
                            <p class="lead">Aqui estão todas as postagens que você criou.</p>
                            <hr>
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div id="lista-meus-posts" class="mt-4"></div>
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
        url: urlBase + "/api/posts/me",
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#loader").addClass("d-none"); // Oculta o loader
            if (response.status === "success" && response.data) {
                let posts = Array.isArray(response.data) ? response.data : [response.data];
                if(posts.length > 0){
                    let html = '<ul class="list-group">';
                    posts.forEach(post => {
                        html += `<li class="list-group-item">
                                    <a href="post.php?id=${post.id}" style="text-decoration: none; color: inherit;">
                                        <h5>${post.titulo}</h5>
                                        <small class="text-muted">Publicado em ${new Date(post.criado_em).toLocaleDateString()}</small>
                                        <p>${post.conteudo}</p>
                                    </a>
                                </li>`;
                    });
                    html += '</ul>';
                    $("#lista-meus-posts").html(html);
                } else {
                    $("#lista-meus-posts").html('<div class="alert alert-info">Você ainda não criou nenhuma postagem.</div>');
                }
            } else {
                $("#lista-meus-posts").html('<div class="alert alert-info">' + (response.message || 'Nenhuma postagem encontrada.') + '</div>');
            }
        },
        error: function () {
            $("#loader").addClass("d-none"); // Oculta o loader mesmo em caso de erro
            $("#lista-meus-posts").html('<div class="alert alert-danger">Erro ao carregar suas postagens.</div>');
        }
    });
});
</script>
</html>

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
                            <h1 class="mb-4">Postagens Recentes</h1>
                            <p class="lead">Explore as últimas publicações da comunidade.</p>
                            <hr>
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div id="todas-postagens" class="mt-4"></div>
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
<script>
$(document).ready(function () {
    $.ajax({
        url: urlBase + "/api/posts",
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#loader").addClass("d-none"); // Oculta o loader
            if (response.status === "success" && response.data) {
                let posts = Array.isArray(response.data) ? response.data : [response.data];
                if (posts.length > 0) {
                    let html = '<ul class="list-group">';
                    posts.forEach(post => {
                        html += `<li class="list-group-item">
                                    <h5>${post.titulo}</h5>
                                    <small class="text-muted">Publicado por ${post.autor_nome ?? 'Usuário desconhecido'} em ${new Date(post.criado_em).toLocaleDateString()}</small>
                                    <p>${post.conteudo}</p>
                                </li>`;
                    });
                    html += '</ul>';
                    $("#todas-postagens").html(html);
                } else {
                    $("#todas-postagens").html('<div class="alert alert-info">Nenhuma postagem encontrada.</div>');
                }
            } else {
                $("#todas-postagens").html('<div class="alert alert-info">' + (response.message || 'Nenhuma postagem disponível.') + '</div>');
            }
        },
        error: function () {
            $("#loader").addClass("d-none");
            $("#todas-postagens").html('<div class="alert alert-danger">Erro ao carregar postagens.</div>');
        }
    });
});
</script>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
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
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h1 class="mb-0">Postagens Recentes</h1>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.history.back();">
                                    <i class="fa-solid fa-arrow-left"></i> Voltar
                                </button>
                            </div>
                            <p class="lead">Explore as últimas publicações da comunidade.</p>
                            <hr>
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div id="paginacao-controles" class="mt-4 row justify-content-center d-none">
                                <div class="col-auto d-flex align-items-center">
                                    <button id="btn-anterior" class="btn btn-outline-primary me-2">Anterior</button>
                                    <span id="paginacao-indice" class="mx-2"></span>
                                    <button id="btn-proximo" class="btn btn-outline-primary ms-2">Próximo</button>
                                </div>
                            </div>
                            <div id="todas-postagens" class="mt-4"></div>
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
    const postsPorPagina = 5;
    let paginaAtual = 1;
    let posts = [];

    function renderizarPosts() {
        const inicio = (paginaAtual - 1) * postsPorPagina;
        const fim = inicio + postsPorPagina;
        const postsPagina = posts.slice(inicio, fim);

        let html = '<ul class="list-group">';
        postsPagina.forEach(post => {
            html += `<li class="list-group-item">
                        <a href="post.php?id=${post.id}" style="text-decoration: none; color: inherit;">
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="mb-0">${post.titulo}</h5>
                                    <small class="text-muted">por: ${post.autor_nome}</small>
                                    <p class="mb-0">${post.conteudo}</p>
                                </div>
                                <div class="col-md-3 text-end">
                                    <small class="text-muted">Publicado em ${new Date(post.criado_em).toLocaleDateString()}</small>
                                </div>
                            </div>
                        </a>
                    </li>`;
        });
        html += '</ul>';

        $("#todas-postagens").html(html);
        atualizarControles();
    }

    function atualizarControles() {
        $("#paginacao-controles").removeClass("d-none").show();
        const totalPaginas = Math.ceil(posts.length / postsPorPagina);

        // Atualiza texto da paginação
        $("#paginacao-indice").text(`Página ${paginaAtual} de ${totalPaginas}`);

        // Exibe/oculta botão anterior
        if (paginaAtual === 1) {
            $("#btn-anterior").hide();
        } else {
            $("#btn-anterior").show();
        }

        // Exibe/oculta botão próximo
        if (paginaAtual >= totalPaginas || posts.length <= postsPorPagina) {
            $("#btn-proximo").hide();
        } else {
            $("#btn-proximo").show();
        }

        // Oculta controles se não precisar paginar
        if (totalPaginas <= 1) {
            $("#paginacao-controles").hide();
        } else {
            $("#paginacao-controles").show();
        }
    }

    $("#btn-anterior").click(function () {
        if (paginaAtual > 1) {
            paginaAtual--;
            renderizarPosts();
        }
    });

    $("#btn-proximo").click(function () {
        const totalPaginas = Math.ceil(posts.length / postsPorPagina);
        if (paginaAtual < totalPaginas) {
            paginaAtual++;
            renderizarPosts();
        }
    });

    // Carrega os posts da API
    $.ajax({
        url: urlBase + "/api/posts",
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#loader").addClass("d-none");
            if (response.status === "success" && response.data) {
                posts = Array.isArray(response.data) ? response.data : [response.data];
                if (posts.length > 0) {
                    renderizarPosts();
                } else {
                    $("#todas-postagens").html('<div class="alert alert-info">Nenhuma postagem encontrada.</div>');
                    $("#paginacao-controles").hide();
                }
            } else {
                $("#todas-postagens").html('<div class="alert alert-info">' + (response.message || 'Nenhuma postagem disponível.') + '</div>');
                $("#paginacao-controles").hide();
            }
        },
        error: function () {
            $("#loader").addClass("d-none");
            $("#todas-postagens").html('<div class="alert alert-danger">Erro ao carregar postagens.</div>');
            $("#paginacao-controles").hide();
        }
    });
});
</script>


</html>

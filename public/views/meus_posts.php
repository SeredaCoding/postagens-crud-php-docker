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
                                <h1 class="mb-0">Minhas Postagens</h1>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.history.back();">
                                    <i class="fa-solid fa-arrow-left"></i> Voltar
                                </button>
                            </div>
                            <p class="lead">Aqui estão todas as postagens que você criou.</p>
                            <hr>
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div id="paginacao-meus-posts" class="mt-4 d-none">
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-auto">
                                        <button id="btn-anterior-meus-posts" class="btn btn-outline-primary">Anterior</button>
                                    </div>
                                    <div class="col-auto text-center">
                                        <span id="paginacao-indice-meus-posts"></span>
                                    </div>
                                    <div class="col-auto">
                                        <button id="btn-proximo-meus-posts" class="btn btn-outline-primary">Próximo</button>
                                    </div>
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
    const postsPorPagina = 5;
    let paginaAtual = 1;
    let posts = [];

    function renderizarMinhasPostagens() {
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

        $("#lista-meus-posts").html(html);
        atualizarControles();
    }

    function atualizarControles() {
        $("#paginacao-meus-posts").removeClass("d-none").show();

        const totalPaginas = Math.ceil(posts.length / postsPorPagina);

        // Atualiza índice de paginação
        $("#paginacao-indice-meus-posts").text(`Página ${paginaAtual} de ${totalPaginas}`);

        // Oculta ou mostra botões de acordo com as regras
        if (paginaAtual === 1) {
            $("#btn-anterior-meus-posts").hide();
        } else {
            $("#btn-anterior-meus-posts").show();
        }

        if (paginaAtual >= totalPaginas || posts.length <= postsPorPagina) {
            $("#btn-proximo-meus-posts").hide();
        } else {
            $("#btn-proximo-meus-posts").show();
        }

        // Se só tem 1 página, oculta tudo
        if (totalPaginas <= 1) {
            $("#paginacao-meus-posts").hide();
        } else {
            $("#paginacao-meus-posts").show();
        }
    }

    $("#btn-anterior-meus-posts").click(function () {
        if (paginaAtual > 1) {
            paginaAtual--;
            renderizarMinhasPostagens();
        }
    });

    $("#btn-proximo-meus-posts").click(function () {
        const totalPaginas = Math.ceil(posts.length / postsPorPagina);
        if (paginaAtual < totalPaginas) {
            paginaAtual++;
            renderizarMinhasPostagens();
        }
    });

    // Carrega postagens do usuário
    $.ajax({
        url: urlBase + "/api/posts/todos-meus",
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#loader").addClass("d-none");
            if (response.status === "success" && response.data) {
                posts = Array.isArray(response.data) ? response.data : [response.data];
                if (posts.length > 0) {
                    renderizarMinhasPostagens();
                } else {
                    $("#lista-meus-posts").html('<div class="alert alert-info">Você ainda não criou nenhuma postagem.</div>');
                    $("#paginacao-meus-posts").hide();
                }
            } else {
                $("#lista-meus-posts").html('<div class="alert alert-info">' + (response.message || 'Nenhuma postagem encontrada.') + '</div>');
                $("#paginacao-meus-posts").hide();
            }
        },
        error: function () {
            $("#loader").addClass("d-none");
            $("#lista-meus-posts").html('<div class="alert alert-danger">Erro ao carregar suas postagens.</div>');
            $("#paginacao-meus-posts").hide();
        }
    });
});
</script>

</html>

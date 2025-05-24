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
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h1 class="mb-4">Criar nova postagem</h1>
                            <form id="form-postagem">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" maxlength="45" required>
                                    <small id="contador-titulo" class="form-text text-muted">0 / 45 caracteres</small>
                                </div>
                                <div class="mb-3">
                                    <label for="conteudo" class="form-label">Conteúdo</label>
                                    <textarea maxlength="300" class="form-control" id="conteudo" name="conteudo" rows="6" required></textarea>
                                    <small id="contador-caracteres" class="form-text text-muted">0 / 300 caracteres</small>
                                </div>
                                <div id="mensagem" class="mb-3"></div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" id="btn-publicar">
                                        <i class="fas fa-spinner fa-spin me-2 d-none" id="loader-icon"></i>
                                        <i class="fa-solid fa-paper-plane" id="icone-aviao"></i> Publicar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.history.back();">
                                        <i class="fa-solid fa-arrow-left"></i> Voltar
                                    </button>
                                </div>
                            </form>
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
    const $titulo = $("#titulo");
    const $contadorTitulo = $("#contador-titulo");
    const $conteudo = $("#conteudo");
    const $contadorConteudo = $("#contador-caracteres");
    const $mensagem = $("#mensagem");
    const $botao = $("#form-postagem button[type='submit']");

    // Contador título
    $titulo.on("input", function () {
        const comprimento = $titulo.val().length;
        $contadorTitulo.text(`${comprimento} / 45 caracteres`);
        if (comprimento > 45) {
            $contadorTitulo.addClass("text-danger");
            $mensagem.html('<div class="alert alert-warning">O título não pode ultrapassar 45 caracteres.</div>');
            $botao.prop("disabled", true);
        } else {
            $contadorTitulo.removeClass("text-danger");
            $mensagem.html('');
            $botao.prop("disabled", false);
        }
    });

    // Contador conteúdo (já existente)
    $conteudo.on("input", function () {
        const comprimento = $conteudo.val().length;
        $contadorConteudo.text(`${comprimento} / 300 caracteres`);
        if (comprimento > 300) {
            $contadorConteudo.addClass("text-danger");
            $mensagem.html('<div class="alert alert-warning">O conteúdo não pode ultrapassar 300 caracteres.</div>');
            $botao.prop("disabled", true);
        } else {
            $contadorConteudo.removeClass("text-danger");
            $mensagem.html('');
            $botao.prop("disabled", false);
        }
    });
    $("#form-postagem").on("submit", function (e) {
        e.preventDefault();

        const dados = {
            titulo: $("#titulo").val().trim(),
            conteudo: $("#conteudo").val().trim()
        };

        if (!dados.titulo || !dados.conteudo) {
            $("#mensagem").html('<div class="alert alert-warning">Preencha todos os campos.</div>');
            return;
        }

        const $loader = $("#loader-icon");
        const $iconeAviao = $("#icone-aviao");
        const $botao = $("#btn-publicar");

        $loader.removeClass("d-none");
        $iconeAviao.addClass("d-none");
        $botao.prop("disabled", true);

        $.ajax({
            url: urlBase + "/api/posts",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(dados),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#mensagem").html('<div class="alert alert-success">Postagem criada com sucesso!</div>');
                    $("#form-postagem")[0].reset();
                    $("#contador-titulo").text("0 / 45 caracteres");
                    $("#contador-caracteres").text("0 / 300 caracteres");
                } else {
                    $("#mensagem").html('<div class="alert alert-danger">' + (response.message || 'Erro ao criar postagem.') + '</div>');
                }
            },
            error: function () {
                $("#mensagem").html('<div class="alert alert-danger">Erro na requisição. Tente novamente.</div>');
            },
            complete: function () {
                $loader.addClass("d-none");
                $iconeAviao.removeClass("d-none");
                $botao.prop("disabled", false);
            }
        });
    });

});
</script>
</html>
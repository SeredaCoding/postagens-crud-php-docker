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
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h1 class="mb-4">Criar nova postagem</h1>
                            <form id="form-postagem">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="conteudo" class="form-label">Conteúdo</label>
                                    <textarea class="form-control" id="conteudo" name="conteudo" rows="6" required></textarea>
                                    <small id="contador-caracteres" class="form-text text-muted">0 / 300 caracteres</small>
                                </div>
                                <div id="mensagem" class="mb-3"></div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-paper-plane"></i> Publicar
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.history.back();">
                                    <i class="fa-solid fa-arrow-left"></i> Voltar
                                </button>
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
    const $conteudo = $("#conteudo");
    const $contador = $("#contador-caracteres");
    const $mensagem = $("#mensagem");
    const $botao = $("#form-postagem button[type='submit']");

    $conteudo.on("input", function () {
        const texto = $conteudo.val();
        const comprimento = texto.length;

        $contador.text(`${comprimento} / 300 caracteres`);

        if (comprimento > 300) {
            $contador.addClass("text-danger");
            $mensagem.html('<div class="alert alert-warning">O conteúdo não pode ultrapassar 300 caracteres.</div>');
            $botao.prop("disabled", true);
        } else {
            $contador.removeClass("text-danger");
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
                } else {
                    $("#mensagem").html('<div class="alert alert-danger">' + (response.message || 'Erro ao criar postagem.') + '</div>');
                }
            },
            error: function () {
                $("#mensagem").html('<div class="alert alert-danger">Erro na requisição. Tente novamente.</div>');
            }
        });
    });
});
</script>
</html>

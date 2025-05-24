<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once(__DIR__ . '/snippets/header.html');

$id = $_GET['id'] ?? null;
?>
<body class="d-flex flex-column min-vh-100">
    <?php 
    require_once(__DIR__.'/snippets/menu.php'); 
    if (!$id) {
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.body.innerHTML = `
                    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="alert alert-danger text-center mt-5 shadow">
                                        <h4 class="alert-heading mb-3"><i class="fa-solid fa-triangle-exclamation"></i> Ops!</h4>
                                        <p>ID da postagem não foi encontrado.<br>
                                        Por favor, volte e selecione uma postagem válida.</p>
                                        <a onclick="window.history.back();" class="btn btn-primary mt-3">
                                            <i class="fa-solid fa-arrow-left"></i> Voltar para Meus Posts
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                `;
            });
        </script>
        <?php
        require_once(__DIR__ . '/snippets/footer.html');
        exit;
    }
    ?>
    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow mt-5">
                        <div class="card-body" id="post-container">
                            <div id="loader" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once(__DIR__ . '/snippets/footer.html'); ?>
</body>

<script>
const postId = <?= (int)$id ?>;
const usuarioId = <?= (int)$_SESSION['usuario_id'] ?>;
let post = null; // variável global para o post

$(document).ready(function () {
    $.getJSON(`${urlBase}/api/posts/${postId}`)
    .done(function (response) {
        // Remover loader somente aqui, após processar a resposta
        if (response.status === "success") {
            post = response.data;
            const isAuthor = post.usuario_id == usuarioId;
            const isAdmin = <?= (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) ? 'true' : 'false' ?>;

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Detalhes da Postagem</h2>
                    <a href="home.php" class="btn btn-outline-secondary ms-2">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div id="post-view">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">${post.titulo}</h2>
                            <small class="text-muted">por: ${post.autor_nome}</small>
                        </div>
                        <small class="text-muted ms-3">Publicado em ${new Date(post.criado_em).toLocaleDateString()}</small>
                    </div>
                    <p class="mt-3">${post.conteudo}</p>
                </div>
            `;


            if (isAuthor || isAdmin) {
                html += `
                    <div class="mt-4 text-end">
                        <button class="btn btn-primary me-2 btn-editar" onclick="habilitarEdicao('${post.titulo}', \`${post.conteudo.replace(/`/g, '\\`')}\`)">
                            Editar <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-danger btn-excluir" onclick="excluirPost(${post.id})" id="btn-excluir">
                            <i class="fas fa-spinner fa-spin me-2 d-none" id="loader-icon"></i>
                            Excluir <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <form id="form-edicao" class="mt-4 d-none">
                        <div class="mb-3">
                            <label>Título</label>
                            <input type="text" class="form-control" id="edit-titulo" maxlength="45">
                            <small id="edit-contador-titulo" class="form-text text-muted">0 / 45 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label>Conteúdo</label>
                            <textarea class="form-control" id="edit-conteudo" rows="5" maxlength="300"></textarea>
                            <small id="edit-contador-caracteres" class="form-text text-muted">0 / 300 caracteres</small>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" id="btn-salvar" class="btn btn-success">
                                <span id="btn-text">Salvar Alterações</span>
                                <span id="btn-loader" class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelarEdicao()">Cancelar</button>
                        </div>
                    </form>
                `;
            }

            $("#post-container").html(html);
        } else {
            // Se a mensagem da API for "Postagem não encontrada." exibe mensagem customizada
            if (response.message === "Postagem não encontrada.") {
                $("#post-container").html(`
                    <div class="alert alert-danger text-center mt-5 shadow">
                        <h4 class="alert-heading mb-3"><i class="fa-solid fa-triangle-exclamation"></i> Ops!</h4>
                        <p>ID da postagem não foi encontrado.<br>
                        Por favor, volte e selecione uma postagem válida.</p>
                        <a onclick="window.history.back();" class="btn btn-primary mt-3">
                            <i class="fa-solid fa-arrow-left"></i> Voltar para Meus Posts
                        </a>
                    </div>
                `);
            } else {
                // Mensagem genérica para outros erros
                $("#post-container").html(`<div class="alert alert-warning">${response.message}</div>`);
            }
        }
        $("#loader").addClass("d-none");
    })
    .fail(function () {
        // Caso a requisição falhe (erro 404, 500, etc)
        $("#post-container").html(`
            <div class="alert alert-danger text-center mt-5 shadow">
                <h4 class="alert-heading mb-3"><i class="fa-solid fa-triangle-exclamation"></i> Erro!</h4>
                <p>Não foi possível carregar a postagem no momento. Tente novamente mais tarde.</p>
            </div>
        `);
        $("#loader").addClass("d-none");
    });
});

$('#post-container').on('click', '.btn-editar', function () {
    if (post) {
        habilitarEdicao(post.titulo, post.conteudo);
    }
});

$(document).on("input", "#edit-titulo, #edit-conteudo", function () {
    const id = $(this).attr("id");
    const texto = $(this).val();
    const comprimento = texto.length;

    let limite = 0;
    let $contador = null;

    if (id === "edit-titulo") {
        limite = 45;
        $contador = $("#edit-contador-titulo");
    } else {
        limite = 300;
        $contador = $("#edit-contador-caracteres");
    }

    $contador.text(`${comprimento} / ${limite} caracteres`);

    const $botaoSalvar = $("#btn-salvar");

    if (comprimento > limite) {
        $contador.addClass("text-danger");
        $botaoSalvar.prop("disabled", true);
    } else {
        $contador.removeClass("text-danger");

        // Verifica se o outro campo também está dentro do limite antes de habilitar o botão
        const tituloValido = $("#edit-titulo").val().length <= 45;
        const conteudoValido = $("#edit-conteudo").val().length <= 300;

        if (tituloValido && conteudoValido) {
            $botaoSalvar.prop("disabled", false);
        }
    }
});

function habilitarEdicao(titulo, conteudo) {
    $('#edit-titulo').val(titulo);
    $('#edit-conteudo').val(conteudo);
    $('#form-edicao').removeClass('d-none');
    $('#post-view').hide();

    // Esconde os botões de editar e excluir
    $('.btn-editar,.btn-excluir').hide();
    // Atualiza o contador de caracteres
    $('#edit-conteudo').trigger('input');
    $('#edit-titulo').trigger('input');
}

function cancelarEdicao() {
    $('#form-edicao').addClass('d-none');
    $('#post-view').show();

    // Mostra os botões de editar e excluir novamente
    $('.btn-editar,.btn-excluir').show();
}

$('#post-container').on('submit', '#form-edicao', function (e) {
    e.preventDefault();

    const titulo = $('#edit-titulo').val().trim();
    const conteudo = $('#edit-conteudo').val().trim();

    if (!titulo || !conteudo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Título e conteúdo não podem estar vazios.'
        });
        return;
    }
    if (titulo.length > 45) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'O título não pode ultrapassar 45 caracteres.'
        });
        return;
    }

    if (conteudo.length > 300) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'O conteúdo não pode ultrapassar 300 caracteres.'
        });
        return;
    }

    // Mostrar loader dentro do botão, desabilitar botão
    $('#btn-text').addClass('d-none');
    $('#btn-loader').removeClass('d-none');
    $('#btn-salvar').prop('disabled', true);

    $.ajax({
        url: `${urlBase}/api/posts/${postId}`,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({ titulo, conteudo }),
        success: function (response) {
            // Esconder loader e habilitar botão
            $('#btn-text').removeClass('d-none');
            $('#btn-loader').addClass('d-none');
            $('#btn-salvar').prop('disabled', false);

            if (response.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Postagem atualizada!',
                    text: 'As alterações foram salvas com sucesso.',
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message || "Erro ao atualizar.",
                });
            }
        },
        error: function () {
            // Esconder loader e habilitar botão
            $('#btn-text').removeClass('d-none');
            $('#btn-loader').addClass('d-none');
            $('#btn-salvar').prop('disabled', false);

            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao enviar dados para o servidor.',
            });
        }
    });
});


function excluirPost(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Essa ação não poderá ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar spinner
            $("#loader-icon").removeClass("d-none");
            $("#btn-excluir").prop("disabled", true);

            $.ajax({
                url: `${urlBase}/api/posts/${id}`,
                type: "DELETE",
                success: function (response) {
                    $("#loader-icon").addClass("d-none");
                    $("#btn-excluir").prop("disabled", false);

                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: 'A postagem foi excluída com sucesso.'
                        }).then(() => {
                            window.location.href = "meus_posts.php";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: response.message || "Erro ao excluir."
                        });
                    }
                },
                error: function () {
                    $("#loader-icon").addClass("d-none");
                    $("#btn-excluir").prop("disabled", false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao excluir a postagem.'
                    });
                }
            });
        }
    });
}

</script>
</html>
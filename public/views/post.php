<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once(__DIR__ . '/snippets/header.html');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='alert alert-danger'>ID da postagem não fornecido.</div>";
    exit;
}
?>
<body class="d-flex flex-column vh-100">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

$(document).ready(function () {
    $.getJSON(`${urlBase}/api/posts/${postId}`, function (response) {
        $("#loader").addClass("d-none");

        if (response.status === "success") {
            const post = response.data;
            const isAuthor = post.usuario_id == usuarioId;

            let html = `
                <div id="post-view">
                    <h2>${post.titulo}</h2>
                    <small class="text-muted">Publicado em ${new Date(post.criado_em).toLocaleDateString()}</small>
                    <p class="mt-3">${post.conteudo}</p>
                </div>
            `;

            if (isAuthor) {
                html += `
                    <div class="mt-4">
                        <button class="btn btn-primary me-2" onclick="habilitarEdicao('${post.titulo}', \`${post.conteudo.replace(/`/g, '\\`')}\`)">
                            Editar <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-danger" onclick="excluirPost(${post.id})">
                            Excluir <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    <form id="form-edicao" class="mt-4 d-none">
                        <div class="mb-3">
                            <label>Título</label>
                            <input type="text" class="form-control" id="edit-titulo">
                        </div>
                        <div class="mb-3">
                            <label>Conteúdo</label>
                            <textarea class="form-control" id="edit-conteudo" rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelarEdicao()">Cancelar</button>
                    </form>
                `;
            }

            $("#post-container").html(html);
        } else {
            $("#post-container").html(`<div class="alert alert-warning">${response.message}</div>`);
        }
    });
});

function habilitarEdicao(titulo, conteudo) {
    $('#edit-titulo').val(titulo);
    $('#edit-conteudo').val(conteudo);
    $('#form-edicao').removeClass('d-none');
    $('#post-view').hide();
}

function cancelarEdicao() {
    $('#form-edicao').addClass('d-none');
    $('#post-view').show();
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

    $.ajax({
        url: `${urlBase}/api/posts/${postId}`,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({ titulo, conteudo }),
        success: function (response) {
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
            $.ajax({
                url: `${urlBase}/api/posts/${id}`,
                type: "DELETE",
                success: function (response) {
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

<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: home.php");
    exit;
}
require_once(__DIR__.'/snippets/header.html');
?>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="authTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Registro</button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="authTabsContent">
                            <!-- Login -->
                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <input type="email" class="form-control" id="login-email" placeholder="&#xf0e0; E-mail" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" id="login-senha" placeholder="&#xf023; Senha" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" id="btn-login">
                                            <span class="btn-text">Entrar <i class="fa-solid fa-arrow-right"></i></span>
                                            <span class="btn-loader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                                        </button>
                                    </div>
                                    <div id="login-mensagem" class="mt-3 text-center"></div>
                                </form>
                            </div>
                            <!-- Registro -->
                            <div class="tab-pane fade" id="register" role="tabpanel">
                                <form id="registerForm">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="register-nome" placeholder="&#xf007; Nome completo" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" id="register-email" placeholder="&#xf0e0; E-mail" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" id="register-senha" placeholder="&#xf023; Senha" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" id="register-confirmar-senha" placeholder="&#xf023; Confirmar Senha" style="font-family:Arial, FontAwesome" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success" id="btn-register">
                                            <span class="btn-text">Registrar <i class="fa-solid fa-check"></i></span>
                                            <span class="btn-loader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                                        </button>
                                    </div>
                                    <div id="register-mensagem" class="mt-3 text-center"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Login
        $('#loginForm').submit(function (event) {
            event.preventDefault();
            
            const btn = $('#btn-login');
            btn.prop('disabled', true);
            btn.find('.btn-text').hide();
            btn.find('.btn-loader').show();

            let email = $('#login-email').val();
            let senha = $('#login-senha').val();

            $.ajax({
                url: urlBase + "/api/login",
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({ email: email, senha: senha }),
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = 'home.php';
                    } else {
                        $('#login-mensagem').html('<div class="alert alert-danger">' + (response.message || 'Erro desconhecido.') + '</div>');
                        btn.prop('disabled', false);
                        btn.find('.btn-text').show();
                        btn.find('.btn-loader').hide();
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    const mensagem = response?.message || "Erro ao conectar com o servidor.";
                    $('#login-mensagem').html('<div class="alert alert-danger">' + mensagem + '</div>');
                    btn.prop('disabled', false);
                    btn.find('.btn-text').show();
                    btn.find('.btn-loader').hide();
                }
            });
        });

        // Registro
        $('#registerForm').submit(function (event) {
            event.preventDefault();

            const btn = $('#btn-register');

            let nome = $('#register-nome').val().trim();
            let email = $('#register-email').val().trim();
            let senha = $('#register-senha').val();
            let confirmarSenha = $('#register-confirmar-senha').val();

            // Validação: nome deve conter apenas letras (maiúsculas, minúsculas, acentos e espaços)
            const nomeRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
            if (!nomeRegex.test(nome)) {
                $('#register-mensagem').html('<div class="alert alert-danger">O nome deve conter apenas letras e espaços.</div>');
                return;
            }

            if (senha !== confirmarSenha) {
                $('#register-mensagem').html('<div class="alert alert-danger">As senhas não coincidem.</div>');
                return;
            }

            btn.prop('disabled', true);
            btn.find('.btn-text').hide();
            btn.find('.btn-loader').show();

            $.ajax({
                url: urlBase + "/api/register",
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({ nome: nome, email: email, senha: senha }),
                success: function (response) {
                    if (response.status === 'success') {
                        $('#register-mensagem').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#registerForm')[0].reset();
                        window.location.href = 'home.php';
                    } else {
                        let erro = response.errors?.email || response.errors?.nome || response.errors?.senha || response.message;
                        $('#register-mensagem').html('<div class="alert alert-danger">' + erro + '</div>');
                        btn.prop('disabled', false);
                        btn.find('.btn-text').show();
                        btn.find('.btn-loader').hide();
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    const mensagem = response?.message || "Erro ao conectar com o servidor.";
                    $('#register-mensagem').html('<div class="alert alert-danger">' + mensagem + '</div>');
                    btn.prop('disabled', false);
                    btn.find('.btn-text').show();
                    btn.find('.btn-loader').hide();
                }
            });
        });
    </script>
</body>
</html>
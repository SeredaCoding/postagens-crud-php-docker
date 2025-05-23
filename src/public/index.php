<?php require_once(__DIR__.'/views/snippets/header.html'); ?>
<body class="d-flex align-items-center justify-content-center vh-100">
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
                                        <button type="submit" class="btn btn-primary">Entrar <i class="fa-solid fa-arrow-right"></i></button>
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
                                        <button type="submit" class="btn btn-success">Registrar <i class="fa-solid fa-check"></i></button>
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
        $(document).ready(function() {
            $('#loginForm').submit(function(event) {
                event.preventDefault();
                let email = $('#login-email').val();
                let senha = $('#login-senha').val();
                
                $.ajax({
                    url: urlBase + "/api/login",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ email: email, senha: senha }),
                    success: function(response) {
                        if (response.success) {
                            $('#login-mensagem').html('<div class="alert alert-success">' + response.message + '</div>');          
                            window.location.href = 'home.php';
                            
                        } else {
                            $('#login-mensagem').html('<div class="alert alert-danger">' + response.error + '</div>');
                        }
                    },
                    error: function() {
                        $('#login-mensagem').html('<div class="alert alert-danger">Erro ao conectar com o servidor.</div>');
                    }
                });
            });

            $('#registerForm').submit(function(event) {
                event.preventDefault();
                let email = $('#register-email').val();
                let senha = $('#register-senha').val();
                let confirmarSenha = $('#register-confirmar-senha').val();
                
                if (senha !== confirmarSenha) {
                    $('#register-mensagem').html('<div class="alert alert-danger">As senhas não coincidem.</div>');
                    return;
                }
                
                $.ajax({
                    url: urlBase + "/api/register",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ email: email, senha: senha }),
                    success: function(response) {
                        if (response.success) {
                            $('#register-mensagem').html('<div class="alert alert-success">' + response.message + '</div>');
                            $('#registerForm')[0].reset();
                            window.location.href = 'home.php';
                        } else {
                            $('#register-mensagem').html('<div class="alert alert-danger">' + response.error + '</div>');
                        }
                    },
                    error: function() {
                        $('#register-mensagem').html('<div class="alert alert-danger">Erro ao conectar com o servidor.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
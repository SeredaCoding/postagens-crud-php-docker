<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function register($data) {
        $this->user->nome = $data->nome ?? null;
        $this->user->email = $data->email ?? null;
        $this->user->senha = $data->senha ?? null;

        if (!$this->user->nome || !$this->user->email || !$this->user->senha) {
            http_response_code(400);
            return ["status" => "error", "message" => "Campos obrigatórios ausentes."];
        }

        // Verifica se o email já existe
        if ($this->user->emailExists($this->user->email)) {
            http_response_code(409); // Conflito
            return ["status" => "error", "message" => "Email já cadastrado."];
        }

        if ($this->user->create()) {
            $usuario = $this->user->login(); // Retorna o usuário criado
            if ($usuario) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                http_response_code(201);
                return ["status" => "success", "message" => "Usuário criado com sucesso."];
            } else {
                http_response_code(500);
                return ["status" => "error", "message" => "Erro ao autenticar após registro."];
            }
        }

        http_response_code(500);
        return ["status" => "error", "message" => "Erro ao criar usuário."];
    }
    
    public function login($data) {
        $this->user->email = $data->email ?? null;
        $this->user->senha = $data->senha ?? null;

        if (!$this->user->email || !$this->user->senha) {
            http_response_code(400);
            return ["status" => "error", "message" => "Campos obrigatórios ausentes."];
        }

        $result = $this->user->login();
        if ($result) {
            // Inicia a sessão e registra dados do usuário
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['usuario_id'] = $result['id'];
            $_SESSION['usuario_nome'] = $result['nome'];
            $_SESSION['usuario_email'] = $result['email'];

            http_response_code(200);
            return ["status" => "success", "data" => $result];
        }

        http_response_code(401);
        return ["status" => "error", "message" => "Email ou senha inválidos."];
    }


    public function list() {
        return $this->user->readAll();
    }

    public function show($id) {
        return $this->user->readById($id);
    }

    public function update($id, $data) {
        $this->user->nome = $data->nome ?? null;
        $this->user->email = $data->email ?? null;

        if ($this->user->update($id)) {
            return ["status" => "success", "message" => "Usuário atualizado com sucesso."];
        }

        return ["status" => "error", "message" => "Erro ao atualizar usuário."];
    }

    public function delete($id) {
        if ($this->user->delete($id)) {
            return ["status" => "success", "message" => "Usuário deletado."];
        }

        return ["status" => "error", "message" => "Erro ao deletar usuário."];
    }
}

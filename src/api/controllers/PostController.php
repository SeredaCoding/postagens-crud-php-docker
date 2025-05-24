<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
    private $post;

    public function __construct($db) {
        $this->post = new Post($db);
    }

    public function create($data) {
        session_start();

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            return ["status" => "error", "message" => "Usuário não autenticado."];
        }

        $this->post->titulo = $data->titulo ?? null;
        $this->post->conteudo = $data->conteudo ?? null;
        $this->post->usuario_id = $_SESSION['usuario_id'];

        if (!$this->post->titulo || !$this->post->conteudo) {
            http_response_code(400);
            return ["status" => "error", "message" => "Título e conteúdo são obrigatórios."];
        }

        if ($this->post->create()) {
            http_response_code(201);
            return ["status" => "success", "message" => "Postagem criada com sucesso."];
        }

        http_response_code(500);
        return ["status" => "error", "message" => "Erro ao criar postagem."];
    }

    public function list() {
        $posts = $this->post->readAll();

        http_response_code(200);
        return [
            "status" => "success",
            "data" => $posts
        ];
    }


    public function show($id) {
        $postagem = $this->post->readById($id);
        if ($postagem) {
            http_response_code(200);
            return ["status" => "success", "data" => $postagem];
        }

        http_response_code(404);
        return ["status" => "error", "message" => "Postagem não encontrada."];
    }


    public function update($id, $data) {
        session_start();

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            return ["status" => "error", "message" => "Usuário não autenticado."];
        }

        $this->post->titulo = $data->titulo ?? null;
        $this->post->conteudo = $data->conteudo ?? null;

        if ($this->post->update($id, $_SESSION['usuario_id'])) {
            return ["status" => "success", "message" => "Postagem atualizada com sucesso."];
        }

        return ["status" => "error", "message" => "Erro ao atualizar postagem ou permissão negada."];
    }

    public function delete($id) {
        session_start();

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            return ["status" => "error", "message" => "Usuário não autenticado."];
        }

        if ($this->post->delete($id, $_SESSION['usuario_id'])) {
            return ["status" => "success", "message" => "Postagem deletada com sucesso."];
        }

        return ["status" => "error", "message" => "Erro ao deletar postagem ou permissão negada."];
    }

   public function meusPosts($limit = '') {
        session_start();

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            return ["status" => "error", "message" => "Usuário não autenticado."];
        }

        $usuario_id = $_SESSION['usuario_id'];
        if($limit !== '' && is_numeric($limit) && $limit > 0) {
            $limit = $limit;
        }

        $posts = $this->post->readByUserId($usuario_id, $limit);

        // Retorna sucesso com array, mesmo vazio
        http_response_code(200);
        return ["status" => "success", "data" => $posts];
    }
}

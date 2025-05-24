<?php

class Post {
    private $conn;
    private $table = "postagens";

    public $id;
    public $titulo;
    public $conteudo;
    public $usuario_id;
    public $criado_em;
    public $editado_em;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (titulo, conteudo, usuario_id, criado_em, editado_em)
                  VALUES (:titulo, :conteudo, :usuario_id, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':conteudo', $this->conteudo);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT p.*, u.nome as autor_nome
                FROM {$this->table} p
                JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.criado_em DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function readById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByUserId($usuario_id, $limit = '') {
        $query = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id ORDER BY criado_em DESC";
        if (!empty($limit) && is_numeric($limit)) {
            $query .= " LIMIT {$limit}";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        // Retorna array vazio se não encontrar nenhum post
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $usuario_id) {
        $query = "UPDATE {$this->table}
                  SET titulo = :titulo, conteudo = :conteudo, editado_em = NOW()
                  WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':conteudo', $this->conteudo);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuario_id);

        return $stmt->execute();
    }

    public function delete($id, $usuario_id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuario_id);

        return $stmt->execute();
    }
}
?>
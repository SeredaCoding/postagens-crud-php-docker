<?php
class User {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $senha;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        die('Chegou na função register()');
        $query = "INSERT INTO " . $this->table . " SET nome=:nome, email=:email, senha=:senha";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", password_hash($this->senha, PASSWORD_DEFAULT));

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login() {
        $query = "SELECT id, nome, email, senha FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);

        if( $stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->senha, $row['senha'])) {
                    return [
                        'id' => $row['id'],
                        'nome' => $row['nome'],
                        'email' => $row['email']
                    ];
                }
            }
        }
        return false;
    }
}

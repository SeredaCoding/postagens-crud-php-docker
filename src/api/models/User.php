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
        if (!$this->nome || !$this->email || !$this->senha) {
            http_response_code(400);
            return json_encode([
                "status" => "error",
                "message" => "Campos obrigatórios ausentes.",
                "data" => null,
                "errors" => [
                    "nome" => $this->nome ? null : "Nome é obrigatório.",
                    "email" => $this->email ? null : "Email é obrigatório.",
                    "senha" => $this->senha ? null : "Senha é obrigatória."
                ]
            ]);
        }

        $checkQuery = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":email", $this->email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            http_response_code(409); // 409 Conflict
            return json_encode([
                "status" => "error",
                "message" => "E-mail já cadastrado.",
                "data" => null,
                "errors" => [
                    "email" => "Este e-mail já está em uso."
                ]
            ]);
        }

        $query = "INSERT INTO " . $this->table . " SET nome=:nome, email=:email, senha=:senha";
        $stmt = $this->conn->prepare($query);

        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $senhaHash);

        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode([
                "status" => "success",
                "message" => "Usuário criado com sucesso.",
                "data" => null,
                "errors" => null
            ]);
        } else {
            http_response_code(500);
            return json_encode([
                "status" => "error",
                "message" => "Erro ao criar usuário.",
                "data" => null,
                "errors" => null
            ]);
        }
    }

    public function login() {
        if (!$this->email || !$this->senha) {
            http_response_code(400);
            return json_encode([
                "status" => "error",
                "message" => "Campos obrigatórios ausentes.",
                "data" => null,
                "errors" => [
                    "email" => $this->email ? null : "Email é obrigatório.",
                    "senha" => $this->senha ? null : "Senha é obrigatória."
                ]
            ]);
        }

        $query = "SELECT id, nome, email, senha FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->senha, $row['senha'])) {
                http_response_code(200);
                return json_encode([
                    "status" => "success",
                    "message" => "Login efetuado com sucesso.",
                    "data" => [
                        "user" => [
                            "id" => $row['id'],
                            "nome" => $row['nome'],
                            "email" => $row['email']
                        ]
                    ],
                    "errors" => null
                ]);
            }
        }

        http_response_code(401);
        return json_encode([
            "status" => "error",
            "message" => "Usuário ou senha inválidos.",
            "data" => null,
            "errors" => [
                "email" => "Verifique o email e senha informados."
            ]
        ]);
    }
}

# 📘 Projeto Blog Dev - CRUD de Postagens com Docker e PHP

Este é um projeto de gerenciamento de postagens (CRUD) construído com PHP e estruturado para rodar em containers Docker. O sistema possui autenticação básica e uma interface web para criação, edição e exclusão de postagens.

# 🧪 Teste Técnico – Desenvolvedor PHP Júnior

Este projeto foi desenvolvido como parte do processo seletivo para a vaga de Desenvolvedor PHP Júnior, com o objetivo de demonstrar habilidades em PHP puro, estruturação de código, uso de Docker, banco de dados e versionamento.

---

## 📌 Informações de Acesso Admin ( Usuário capaz de excluir e editar postagens de outros usuários)

- **Usuário administrador:** `admin@teste.com`  
- **Senha:** `123`

---


## ⚙️ Tecnologias Utilizadas

PHP 8+

Apache HTTP Server

MySQL 5.7+

Docker / Docker Compose

HTML + PHP Views

Estrutura MVC (Model-View-Controller)
## 📝 Funcionalidades

-Registro de usuários.

-Login e logout de usuários.

-Criação de postagens.

-Edição e exclusão de postagens.

-Visualização de postagens públicas e privadas.

-Separação entre posts do usuário logado e de outros usuários.
## ⚙️ Como Executar o Projeto

### Pré-requisitos

- Docker e Docker Compose instalados.

1. Clone o repositório:
   ```bash
    git clone https://github.com/SeredaCoding/postagens-crud-php-docker.git

2. Navegue até o diretório Docker do projeto:
   ```bash
   cd docker

3. Execute o comando para subir os containers:
   ```bash
   docker-compose up -d

4. Acesse o sistema no navegador:
   ```bash
    http://localhost:8000/

## 📁 Estrutura de Diretórios
    postagens-crud-php-docker/
    ├── README.md
    │
    ├── docker/
    │   ├── 000-default.conf
    │   ├── docker-compose.yml
    │   ├── Dockerfile
    │   └── mysql/
    │       └── initdb/
    │           └── init.sql
    │
    ├── public/
    │   └── views/
    │       ├── criar_post.php
    │       ├── home.php
    │       ├── index.php
    │       ├── logout.php
    │       ├── meus_posts.php
    │       ├── post.php
    │       ├── posts.php
    │       └── snippets/
    │           ├── footer.html
    │           ├── header.html
    │           └── menu.php
    │
    └── src/
        ├── api/
        │   ├── .htaccess
        │   ├── index.php
        │   ├── config/
        │   │   └── db.php
        │   ├── controllers/
        │   │   ├── PostController.php
        │   │   └── UserController.php
        │   ├── models/
        │   │   ├── Post.php
        │   │   └── User.php
        │   └── routes/
        │       └── routes.php

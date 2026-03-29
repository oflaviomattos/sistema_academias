<?php
// ============================================================
// CONTROLLER: Autenticação
// ============================================================

$action = $page; // 'login' ou 'logout'

if ($action === 'logout') {
    session_destroy();
    redirect('login');
}

// Já logado → vai pro dashboard
if (isLoggedIn()) {
    redirect('dashboard');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $stmt = getDB()->prepare(
            "SELECT id, nome, email, senha_hash, perfil, academia_id, ativo
             FROM usuarios WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !$usuario['ativo']) {
            $erro = 'Usuário não encontrado ou inativo.';
        } elseif (!password_verify($senha, $usuario['senha_hash'])) {
            $erro = 'Senha incorreta.';
        } else {
            // Login OK
            session_regenerate_id(true);
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['perfil']       = $usuario['perfil'];
            $_SESSION['academia_id']  = $usuario['academia_id'];
            redirect('dashboard');
        }
    }
}

// Renderiza view de login
require_once __DIR__ . '/../views/auth/login.php';

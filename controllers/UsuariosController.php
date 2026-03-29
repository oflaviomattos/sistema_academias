<?php
requireAdmin();
$model        = new UsuarioModel();
$academiaModel = new AcademiaModel();

switch ($page) {

    case 'usuarios':
        $usuarios  = $model->listar();
        $academias = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/usuarios/index.php';
        break;

    case 'usuarios.criar':
        $usuario   = [];
        $academias = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/usuarios/form.php';
        break;

    case 'usuarios.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('usuarios'); }
        $email = trim($_POST['email'] ?? '');
        $nome  = trim($_POST['nome']  ?? '');
        $senha = $_POST['senha'] ?? '';
        if (!$nome || !$email || !$senha) {
            flashSet('danger', 'Nome, e-mail e senha sao obrigatorios.');
            redirect('usuarios.criar');
        }
        if (strlen($senha) < 6) {
            flashSet('danger', 'A senha deve ter ao menos 6 caracteres.');
            redirect('usuarios.criar');
        }
        if ($model->buscarPorEmail($email)) {
            flashSet('danger', 'Este e-mail ja esta em uso.');
            redirect('usuarios.criar');
        }
        $model->criar([
            'nome'        => $nome,
            'email'       => $email,
            'senha'       => $senha,
            'perfil'      => $_POST['perfil'] ?? 'usuario',
            'academia_id' => $_POST['academia_id'] ?? null,
        ]);
        flashSet('success', 'Usuario criado com sucesso!');
        redirect('usuarios');
        break;

    case 'usuarios.editar':
        $id      = (int)($_GET['id'] ?? 0);
        $usuario = $model->buscarPorId($id);
        if (!$usuario) { flashSet('danger','Usuario nao encontrado.'); redirect('usuarios'); }
        $academias = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/usuarios/form.php';
        break;

    case 'usuarios.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('usuarios'); }
        $id    = (int)$_POST['id'];
        $email = trim($_POST['email'] ?? '');
        // Verifica email duplicado em outro usuario
        $existe = $model->buscarPorEmail($email);
        if ($existe && (int)$existe['id'] !== $id) {
            flashSet('danger', 'Este e-mail ja esta em uso por outro usuario.');
            redirect('usuarios.editar&id=' . $id);
        }
        $model->atualizar($id, [
            'nome'        => trim($_POST['nome'] ?? ''),
            'email'       => $email,
            'perfil'      => $_POST['perfil'] ?? 'usuario',
            'academia_id' => $_POST['academia_id'] ?? null,
            'ativo'       => $_POST['ativo'] ?? 1,
        ]);
        // Troca de senha opcional
        $novaSenha = $_POST['nova_senha'] ?? '';
        if ($novaSenha) {
            if (strlen($novaSenha) < 6) {
                flashSet('warning', 'Dados atualizados, mas a senha nao foi alterada (minimo 6 caracteres).');
            } else {
                $model->alterarSenha($id, $novaSenha);
            }
        }
        flashSet('success', 'Usuario atualizado!');
        redirect('usuarios');
        break;

    case 'usuarios.excluir':
        $id = (int)($_GET['id'] ?? 0);
        // Nao permite excluir o proprio usuario
        if ($id === (int)$_SESSION['usuario_id']) {
            flashSet('danger', 'Voce nao pode excluir seu proprio usuario.');
            redirect('usuarios');
        }
        $model->excluir($id);
        flashSet('success', 'Usuario removido.');
        redirect('usuarios');
        break;
}

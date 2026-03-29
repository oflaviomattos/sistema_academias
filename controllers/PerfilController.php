<?php
requireLogin();
$model = new UsuarioModel();

switch ($page) {

    case 'perfil':
        $usuario = $model->buscarPorId((int)$_SESSION['usuario_id']);
        require_once __DIR__ . '/../views/usuarios/perfil.php';
        break;

    case 'perfil.senha':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('perfil'); }
        $id          = (int)$_SESSION['usuario_id'];
        $senhaAtual  = $_POST['senha_atual']  ?? '';
        $novaSenha   = $_POST['nova_senha']   ?? '';
        $confirma    = $_POST['confirma']     ?? '';
        if ($novaSenha !== $confirma) {
            flashSet('danger', 'A nova senha e a confirmacao nao coincidem.');
            redirect('perfil');
        }
        $resultado = $model->alterarPropriaSenha($id, $senhaAtual, $novaSenha);
        if ($resultado['ok']) {
            flashSet('success', $resultado['msg']);
        } else {
            flashSet('danger', $resultado['msg']);
        }
        redirect('perfil');
        break;
}

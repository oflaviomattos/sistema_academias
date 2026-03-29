<?php
requireAdmin();
$model = new ConfiguracaoModel();

switch ($page) {

    case 'configuracoes':
        $mensagem = $model->get('mensagem_cobranca');
        $chave_pix = $model->get('chave_pix');
        $nome_professor = $model->get('nome_professor');
        require_once __DIR__ . '/../views/configuracoes/index.php';
        break;

    case 'configuracoes.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('configuracoes'); }
        $model->set('mensagem_cobranca', $_POST['mensagem_cobranca'] ?? '');
        $model->set('chave_pix',         trim($_POST['chave_pix'] ?? ''));
        $model->set('nome_professor',    trim($_POST['nome_professor'] ?? ''));
        flashSet('success', 'Configuracoes salvas com sucesso!');
        redirect('configuracoes');
        break;
}

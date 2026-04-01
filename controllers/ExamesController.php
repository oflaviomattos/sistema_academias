<?php
// Exames de Faixa
$exameModel = new ExameModel();
$alunoModel = new AlunoModel();
$faixaModel = new FaixaModel();
$academiaId = getAcademiaFiltro();

switch ($page) {
    case 'exames':
        $exames = $exameModel->listar($academiaId);
        require_once __DIR__ . '/../views/exames/index.php';
        break;
    case 'exames.criar':
        $alunos = $alunoModel->listarParaSelect($academiaId);
        $faixas = $faixaModel->listar();
        require_once __DIR__ . '/../views/exames/form.php';
        break;
    case 'exames.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('exames'); }
        $alunoId = (int)$_POST['aluno_id'];
        $exameModel->criar([
            'aluno_id'   => $alunoId,
            'faixa_atual'=> $_POST['faixa_atual'],
            'nova_faixa' => $_POST['nova_faixa'],
            'data_exame' => $_POST['data_exame'],
            'status'     => 'pendente',
            'observacoes'=> $_POST['observacoes']??null,
        ]);
        flashSet('success','Exame agendado!');
        if (!empty($_POST['veio_do_aluno'])) {
            redirect('alunos.ver&id=' . $alunoId);
        }
        redirect('exames');
        break;
    case 'exames.aprovar':
        $id = (int)($_GET['id']??0);
        $exameModel->aprovar($id);
        flashSet('success','Exame aprovado e faixa atualizada!');
        redirect('exames');
        break;
}

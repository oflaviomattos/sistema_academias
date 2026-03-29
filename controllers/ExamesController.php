<?php
// Exames de Faixa
$exameModel = new ExameModel();
$alunoModel = new AlunoModel();
$academiaId = getAcademiaFiltro();

switch ($page) {
    case 'exames':
        $exames = $exameModel->listar($academiaId);
        require_once __DIR__ . '/../views/exames/index.php';
        break;
    case 'exames.criar':
        $alunos = $alunoModel->listarParaSelect($academiaId);
        require_once __DIR__ . '/../views/exames/form.php';
        break;
    case 'exames.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('exames'); }
        $exameModel->criar([
            'aluno_id'   => (int)$_POST['aluno_id'],
            'faixa_atual'=> $_POST['faixa_atual'],
            'nova_faixa' => $_POST['nova_faixa'],
            'data_exame' => $_POST['data_exame'],
            'status'     => 'pendente',
            'observacoes'=> $_POST['observacoes']??null,
        ]);
        flashSet('success','Exame agendado!');
        redirect('exames');
        break;
    case 'exames.aprovar':
        $id = (int)($_GET['id']??0);
        $exameModel->aprovar($id);
        flashSet('success','Exame aprovado e faixa atualizada!');
        redirect('exames');
        break;
}

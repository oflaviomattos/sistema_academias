<?php
$model = new ResponsavelModel();
$alunoModel = new AlunoModel();

switch ($page) {

    case 'responsaveis':
        $busca        = $_GET['busca'] ?? '';
        $responsaveis = $model->listar($busca);
        $alunosPorResponsavel = [];
        foreach ($responsaveis as $r) {
            $alunosPorResponsavel[(int)$r['id']] = $model->buscarAlunos((int)$r['id']);
        }
        require_once __DIR__ . '/../views/alunos/responsaveis.php';
        break;

    case 'responsaveis.criar':
        $responsavel = [];
        $alunosVinculados = [];
        $todosAlunos = [];
        require_once __DIR__ . '/../views/alunos/responsavel_form.php';
        break;

    case 'responsaveis.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('responsaveis'); }
        $d = ['nome'=>trim($_POST['nome']??''),'telefone'=>trim($_POST['telefone']??''),'email'=>$_POST['email']??null];
        if (!$d['nome']||!$d['telefone']) { flashSet('danger','Nome e telefone obrigatorios.'); redirect('responsaveis.criar'); }
        $novoId = $model->criar($d);
        flashSet('success','Responsavel cadastrado!');
        redirect('responsaveis.editar&id=' . $novoId);
        break;

    case 'responsaveis.editar':
        $id = (int)($_GET['id'] ?? 0);
        $responsavel = $model->buscarPorId($id);
        if (!$responsavel) { flashSet('danger','Nao encontrado.'); redirect('responsaveis'); }
        $alunosVinculados = $model->buscarAlunos($id);
        $todosAlunos      = $alunoModel->listarParaSelect(getAcademiaFiltro());
        require_once __DIR__ . '/../views/alunos/responsavel_form.php';
        break;

    case 'responsaveis.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('responsaveis'); }
        $id = (int)$_POST['id'];
        $d  = ['nome'=>trim($_POST['nome']??''),'telefone'=>trim($_POST['telefone']??''),'email'=>$_POST['email']??null];
        $model->atualizar($id,$d);
        flashSet('success','Responsavel atualizado!');
        redirect('responsaveis.editar&id=' . $id);
        break;

    case 'responsaveis.vincular':
        $alunoId      = (int)($_GET['aluno_id'] ?? 0);
        $responsavelId = (int)($_GET['responsavel_id'] ?? 0);
        if ($alunoId && $responsavelId) {
            vincularAlunoResponsavel($alunoId, $responsavelId);
            flashSet('success','Aluno vinculado!');
        }
        redirect('responsaveis.editar&id=' . $responsavelId);
        break;

    case 'responsaveis.desvincular':
        $alunoId      = (int)($_GET['aluno_id'] ?? 0);
        $responsavelId = (int)($_GET['responsavel_id'] ?? 0);
        if ($alunoId) {
            desvincularAlunoResponsavel($alunoId);
            flashSet('success','Aluno desvinculado.');
        }
        redirect('responsaveis.editar&id=' . $responsavelId);
        break;

    case 'responsaveis.excluir':
        requireAdmin();
        $model->excluir((int)($_GET['id']??0));
        flashSet('success','Responsavel removido.');
        redirect('responsaveis');
        break;
}

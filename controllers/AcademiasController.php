<?php
requireAdmin();
$model = new AcademiaModel();
switch ($page) {
    case 'academias':
        $academias = $model->listar();
        require_once __DIR__ . '/../views/academias/index.php';
        break;
    case 'academias.criar':
        $academia = [];
        require_once __DIR__ . '/../views/academias/form.php';
        break;
    case 'academias.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('academias'); }
        $model->criar(['nome'=>trim($_POST['nome']??''),'cidade'=>$_POST['cidade']??null,'responsavel'=>$_POST['responsavel']??null]);
        flashSet('success','Academia cadastrada!');
        redirect('academias');
        break;
    case 'academias.editar':
        $academia = $model->buscarPorId((int)($_GET['id']??0));
        if (!$academia) { flashSet('danger','Não encontrada.'); redirect('academias'); }
        require_once __DIR__ . '/../views/academias/form.php';
        break;
    case 'academias.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('academias'); }
        $id = (int)$_POST['id'];
        $model->atualizar($id,['nome'=>trim($_POST['nome']??''),'cidade'=>$_POST['cidade']??null,'responsavel'=>$_POST['responsavel']??null,'ativo'=>$_POST['ativo']??1]);
        flashSet('success','Academia atualizada!');
        redirect('academias');
        break;
    case 'academias.excluir':
        $model->excluir((int)($_GET['id']??0));
        flashSet('success','Academia removida.');
        redirect('academias');
        break;
}

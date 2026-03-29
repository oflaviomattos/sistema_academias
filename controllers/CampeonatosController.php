<?php
$campeonatoModel = new CampeonatoModel();
$alunoModel      = new AlunoModel();
$academiaId      = getAcademiaFiltro();

switch ($page) {
    case 'campeonatos':
        $campeonatos = $campeonatoModel->listar();
        require_once __DIR__ . '/../views/campeonatos/index.php';
        break;
    case 'campeonatos.criar':
        $campeonato = [];
        require_once __DIR__ . '/../views/campeonatos/form.php';
        break;
    case 'campeonatos.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('campeonatos'); }
        $id = $campeonatoModel->criar([
            'nome'=>trim($_POST['nome']??''),'data'=>$_POST['data'],'local'=>$_POST['local']??null,'descricao'=>$_POST['descricao']??null
        ]);
        flashSet('success','Campeonato criado!');
        redirect('campeonatos.editar&id='.$id);
        break;
    case 'campeonatos.editar':
        $id          = (int)($_GET['id']??0);
        $campeonato  = $campeonatoModel->buscarPorId($id);
        $inscritos   = $campeonatoModel->buscarInscritos($id);
        $alunos      = $alunoModel->listarParaSelect($academiaId);
        require_once __DIR__ . '/../views/campeonatos/form.php';
        break;
    case 'campeonatos.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('campeonatos'); }
        $id = (int)$_POST['id'];
        // Inscrever alunos selecionados
        if (!empty($_POST['alunos'])) {
            foreach ($_POST['alunos'] as $alunoId) {
                $campeonatoModel->inscreverAluno($id, (int)$alunoId);
            }
        }
        $campeonatoModel->atualizar($id,['nome'=>$_POST['nome'],'data'=>$_POST['data'],'local'=>$_POST['local']??null,'descricao'=>$_POST['descricao']??null]);
        flashSet('success','Campeonato atualizado!');
        redirect('campeonatos');
        break;
}

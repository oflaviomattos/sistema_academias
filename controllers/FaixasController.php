<?php
requireAdmin();
$model = new FaixaModel();

switch ($page) {

    case 'faixas':
        $faixas = $model->listar();
        require_once __DIR__ . '/../views/faixas/index.php';
        break;

    case 'faixas.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('faixas'); }
        $nome  = trim($_POST['nome'] ?? '');
        $cor   = trim($_POST['cor_hex'] ?? '#cccccc');
        $ordem = (int)($_POST['ordem'] ?? 0);
        if (!$nome) { flashSet('danger','Nome obrigatorio.'); redirect('faixas'); }
        $model->criar(['nome'=>$nome,'cor_hex'=>$cor,'ordem'=>$ordem]);
        flashSet('success','Faixa criada!');
        redirect('faixas');
        break;

    case 'faixas.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('faixas'); }
        $id    = (int)$_POST['id'];
        $nome  = trim($_POST['nome'] ?? '');
        $cor   = trim($_POST['cor_hex'] ?? '#cccccc');
        $ordem = (int)($_POST['ordem'] ?? 0);
        $model->atualizar($id, ['nome'=>$nome,'cor_hex'=>$cor,'ordem'=>$ordem]);
        flashSet('success','Faixa atualizada!');
        redirect('faixas');
        break;

    case 'faixas.excluir':
        $id = (int)($_GET['id'] ?? 0);
        // Verifica se algum aluno usa essa faixa
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM alunos WHERE faixa=:f");
        $faixa = $model->buscarPorId($id);
        if ($faixa) {
            $stmt->execute(['f' => $faixa['nome']]);
            $uso = $stmt->fetchColumn();
            if ($uso > 0) {
                flashSet('danger', 'Nao e possivel excluir: ' . $uso . ' aluno(s) usa(m) esta faixa.');
                redirect('faixas');
            }
        }
        $model->excluir($id);
        flashSet('success','Faixa removida.');
        redirect('faixas');
        break;

    case 'faixas.reordenar':
        // Recebe JSON via POST para drag-and-drop
        $json = file_get_contents('php://input');
        $ids  = json_decode($json, true);
        if (is_array($ids)) {
            $model->reordenar($ids);
            header('Content-Type: application/json');
            echo json_encode(['ok'=>true]);
            exit;
        }
        redirect('faixas');
        break;
}

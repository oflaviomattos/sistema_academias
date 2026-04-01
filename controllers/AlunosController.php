<?php
// ============================================================
// CONTROLLER: Alunos
// ============================================================

$model            = new AlunoModel();
$responsavelModel = new ResponsavelModel();
$academiaModel    = new AcademiaModel();
$faixaModel       = new FaixaModel();
$academiaId       = getAcademiaFiltro();

switch ($page) {

    // ---- LISTAGEM ----
    case 'alunos':
        $filtros = [
            'academia_id' => $academiaId ?? ($_GET['academia_id'] ?? null),
            'status'      => $_GET['status'] ?? '',
            'faixa'       => $_GET['faixa'] ?? '',
            'serie'       => $_GET['serie'] ?? '',
            'busca'       => $_GET['busca'] ?? '',
        ];
        $alunos    = $model->listar($filtros);
        $academias = $academiaModel->listarAtivas();
        $faixas    = $faixaModel->listar();
        require_once __DIR__ . '/../views/alunos/index.php';
        break;

    // ---- VISUALIZAR ----
    case 'alunos.ver':
        $id    = (int)($_GET['id'] ?? 0);
        $aluno = $model->buscarPorId($id);
        if (!$aluno) { flashSet('danger','Aluno não encontrado.'); redirect('alunos'); }
        $mensalidadeModel = new MensalidadeModel();
        $mensalidades = $mensalidadeModel->listar(['aluno_id' => $id]);
        // Pendências atrasadas para cobrança
        $pendenciasAtrasadas = array_filter($mensalidades, function($m) {
            return $m['status'] === 'atrasado';
        });
        $totalAtrasado = array_sum(array_column($pendenciasAtrasadas, 'valor'));
        $exameModel   = new ExameModel();
        $exames       = $exameModel->listar($aluno['academia_id']);
        require_once __DIR__ . '/../views/alunos/ver.php';
        break;

    // ---- FORMULÁRIO CRIAR ----
    case 'alunos.criar':
        $responsaveis = $responsavelModel->listarParaSelect();
        $academias    = $academiaModel->listarAtivas();
        $faixas       = $faixaModel->listar();
        $aluno        = [];
        require_once __DIR__ . '/../views/alunos/form.php';
        break;

    // ---- SALVAR NOVO ----
    case 'alunos.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('alunos'); }
        $dados = [
            'nome_completo'   => trim($_POST['nome_completo'] ?? ''),
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
            'turno'           => $_POST['turno'] ?? 'M',
            'contrato_ok'     => $_POST['contrato_ok'] ?? 0,
            'faixa'           => $_POST['faixa'] ?? 'branca',
            'serie_nivel'      => $_POST['serie_nivel'] ?? null,
            'tamanho'          => $_POST['tamanho'] ?? '',
            'peso'             => $_POST['peso'] ?? null,
            'bolsa_percentual' => (int)($_POST['bolsa_percentual'] ?? 0),
            'status'           => $_POST['status'] ?? 'ativo',
            'data_entrada'     => $_POST['data_entrada'] ?? date('Y-m-d'),
            'academia_id'      => $academiaId ?? (int)($_POST['academia_id'] ?? 0),
            'responsavel_id'   => $_POST['responsavel_id'] ?? null,
            'observacoes'      => $_POST['observacoes'] ?? '',
        ];
        if (!$dados['nome_completo'] || !$dados['academia_id']) {
            flashSet('danger', 'Nome e academia são obrigatórios.');
            redirect('alunos.criar');
        }
        $novoId = $model->criar($dados);
        flashSet('success', 'Aluno cadastrado com sucesso!');
        redirect('alunos.ver&id=' . $novoId);
        break;

    // ---- FORMULÁRIO EDITAR ----
    case 'alunos.editar':
        $id    = (int)($_GET['id'] ?? 0);
        $aluno = $model->buscarPorId($id);
        if (!$aluno) { flashSet('danger','Aluno não encontrado.'); redirect('alunos'); }
        $responsaveis = $responsavelModel->listarParaSelect();
        $academias    = $academiaModel->listarAtivas();
        $faixas       = $faixaModel->listar();
        require_once __DIR__ . '/../views/alunos/form.php';
        break;

    // ---- ATUALIZAR ----
    case 'alunos.atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('alunos'); }
        $id = (int)($_POST['id'] ?? 0);
        $dados = [
            'nome_completo'    => trim($_POST['nome_completo'] ?? ''),
            'data_nascimento'  => $_POST['data_nascimento'] ?? '',
            'turno'            => $_POST['turno'] ?? 'M',
            'contrato_ok'      => $_POST['contrato_ok'] ?? 0,
            'faixa'            => $_POST['faixa'] ?? 'branca',
            'serie_nivel'      => $_POST['serie_nivel'] ?? null,
            'tamanho'          => $_POST['tamanho'] ?? '',
            'peso'             => $_POST['peso'] ?? null,
            'bolsa_percentual' => (int)($_POST['bolsa_percentual'] ?? 0),
            'status'           => $_POST['status'] ?? 'ativo',
            'data_entrada'     => $_POST['data_entrada'] ?? date('Y-m-d'),
            'academia_id'      => $academiaId ?? (int)($_POST['academia_id'] ?? 0),
            'responsavel_id'   => $_POST['responsavel_id'] ?? null,
            'observacoes'      => $_POST['observacoes'] ?? '',
        ];
        $model->atualizar($id, $dados);
        flashSet('success', 'Aluno atualizado com sucesso!');
        redirect('alunos.ver&id=' . $id);
        break;

    // ---- EXCLUIR ----
    case 'alunos.excluir':
        requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $model->excluir($id);
        flashSet('success', 'Aluno removido.');
        redirect('alunos');
        break;
}

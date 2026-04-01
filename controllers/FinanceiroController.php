<?php
// ============================================================
// CONTROLLER: Financeiro
// ============================================================
$mensalidadeModel = new MensalidadeModel();
$alunoModel       = new AlunoModel();
$academiaModel    = new AcademiaModel();
$academiaId       = getAcademiaFiltro();

// Atualiza atrasados sempre que acessa financeiro
$mensalidadeModel->atualizarAtrasados();

switch ($page) {

    case 'financeiro':
        $filtros = [
            'academia_id'    => $academiaId ?? ($_GET['academia_id'] ?? null),
            'status'         => $_GET['status'] ?? '',
            'mes_referencia' => $_GET['mes'] ?? date('Y-m'),
            'busca'          => $_GET['busca'] ?? '',
        ];
        $mensalidades = $mensalidadeModel->listar($filtros);
        $academias    = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/financeiro/index.php';
        break;

    case 'financeiro.lancar':
        $alunos = $alunoModel->listarParaSelect($academiaId);
        require_once __DIR__ . '/../views/financeiro/form.php';
        break;

    case 'financeiro.salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('financeiro'); }
        $dados = [
            'aluno_id'        => (int)$_POST['aluno_id'],
            'mes_referencia'  => $_POST['mes_referencia'],
            'valor'           => (float)str_replace(',','.',$_POST['valor']),
            'status'          => $_POST['status'] ?? 'pendente',
            'forma_pagamento' => $_POST['forma_pagamento'] ?? null,
            'data_vencimento' => $_POST['data_vencimento'],
            'data_pagamento'  => $_POST['data_pagamento'] ?? null,
            'observacoes'     => $_POST['observacoes'] ?? null,
        ];
        // Verifica duplicata
        $existe = $mensalidadeModel->buscarPorAlunoMes($dados['aluno_id'], $dados['mes_referencia']);
        if ($existe) {
            flashSet('warning', 'Já existe mensalidade para este aluno neste mês.');
            redirect('financeiro.lancar');
        }
        // Verifica bolsa do aluno
        $alunoBolsa = $alunoModel->buscarPorId($dados['aluno_id']);
        if ($alunoBolsa && (int)$alunoBolsa['bolsa_percentual'] === 100) {
            $dados['valor']  = 0;
            $dados['status'] = 'integral';
            $dados['observacoes'] = trim(($dados['observacoes'] ? $dados['observacoes'] . ' — ' : '') . 'Bolsa integral 100%');
        } elseif ($alunoBolsa && (int)$alunoBolsa['bolsa_percentual'] > 0) {
            $pct     = (int)$alunoBolsa['bolsa_percentual'];
            $desconto = $dados['valor'] * ($pct / 100);
            $dados['valor'] = $dados['valor'] - $desconto;
            $obs = "Bolsa {$pct}%: desconto de R$ " . number_format($desconto, 2, ',', '.') . " (valor original R$ " . number_format($dados['valor'] + $desconto, 2, ',', '.') . ")";
            $dados['observacoes'] = trim(($dados['observacoes'] ? $dados['observacoes'] . ' — ' : '') . $obs);
        }
        $mensalidadeModel->criar($dados);
        flashSet('success', 'Mensalidade lançada com sucesso!');
        redirect('alunos.ver&id=' . $dados['aluno_id']);
        break;

    case 'financeiro.pagar':
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mensalidadeModel->registrarPagamento($id, [
                'forma_pagamento' => $_POST['forma_pagamento'] ?? 'Pix',
                'data_pagamento'  => $_POST['data_pagamento'] ?? date('Y-m-d'),
                'observacoes'     => $_POST['observacoes'] ?? null,
            ]);
            flashSet('success', 'Pagamento registrado!');
            redirect('financeiro&mes=' . ($_POST['mes'] ?? date('Y-m')));
        }
        $mensalidade = $mensalidadeModel->buscarPorId($id);
        require_once __DIR__ . '/../views/financeiro/pagar.php';
        break;

    case 'financeiro.cancelar':
        $id = (int)($_GET['id'] ?? 0);
        $mensalidade = $mensalidadeModel->buscarPorId($id);
        if ($mensalidade) {
            $mensalidadeModel->cancelar($id);
            flashSet('success', 'Cobrança cancelada com sucesso!');
            redirect('alunos.ver&id=' . $mensalidade['aluno_id']);
        }
        flashSet('danger', 'Mensalidade não encontrada.');
        redirect('financeiro');
        break;

    case 'financeiro.excluir':
        requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $mensalidadeModel->excluir($id);
        flashSet('success', 'Lançamento removido.');
        redirect('financeiro');
        break;

    case 'financeiro.gerar':
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acId = (int)$_POST['academia_id'];
            $mes  = $_POST['mes_referencia'];
            $valor = (float)str_replace(',','.',$_POST['valor']);
            $venc  = $_POST['data_vencimento'];
            $criados = $mensalidadeModel->gerarEmLote($acId, $mes, $valor, $venc);
            flashSet('success', "Geradas $criados mensalidades para o mês $mes.");
            redirect('financeiro&mes=' . $mes);
        }
        $academias = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/financeiro/gerar.php';
        break;
}

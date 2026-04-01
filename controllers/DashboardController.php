<?php
$alunoModel       = new AlunoModel();
$mensalidadeModel = new MensalidadeModel();
$exameModel       = new ExameModel();
$campeonatoModel  = new CampeonatoModel();

$mensalidadeModel->atualizarAtrasados();

$academiaId = getAcademiaFiltro();
$mesAtual   = date('Y-m');

$dados = [
    'total_ativos'         => $alunoModel->contarAtivos($academiaId),
    'total_inadimplentes'  => $mensalidadeModel->contarInadimplentes($academiaId),
    'total_recebido_mes'   => $mensalidadeModel->totalRecebidoMes($mesAtual, $academiaId),
    'proximos_vencimentos' => $mensalidadeModel->proximosVencimentos($academiaId),
    'lancamentos_futuros'  => $mensalidadeModel->lancamentosFuturos($academiaId),
    'total_mes_subsequente'=> $mensalidadeModel->totalMesSubsequente($academiaId),
    'proximos_exames'      => $exameModel->proximosExames($academiaId),
    'proximos_campeonatos' => $campeonatoModel->proximosCampeonatos($academiaId),
];

if (isAdmin()) {
    $academiaModel = new AcademiaModel();
    $dados['academias'] = $academiaModel->listarAtivas();
}

// Se clicou no card de inadimplentes, carrega lista para cobrança
$verCobranca = isset($_GET['cobranca']) && $_GET['cobranca'] === '1';
if ($verCobranca) {
    $dados['inadimplentes'] = $mensalidadeModel->listarInadimplentesParaCobranca($academiaId);
}

require_once __DIR__ . '/../views/dashboard/index.php';

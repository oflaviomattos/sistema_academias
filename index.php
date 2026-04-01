<?php
// Mostra erros na tela temporariamente — remova depois de resolver
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
foreach (glob(__DIR__ . '/models/*.php') as $m) { require_once $m; }

$page = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';

$public = ['login','logout'];
if (!in_array($page, $public)) { requireLogin(); }

$routes = [
    'login'                  => 'controllers/AuthController.php',
    'logout'                 => 'controllers/AuthController.php',
    'dashboard'              => 'controllers/DashboardController.php',
    'alunos'                 => 'controllers/AlunosController.php',
    'alunos.criar'           => 'controllers/AlunosController.php',
    'alunos.salvar'          => 'controllers/AlunosController.php',
    'alunos.editar'          => 'controllers/AlunosController.php',
    'alunos.atualizar'       => 'controllers/AlunosController.php',
    'alunos.excluir'         => 'controllers/AlunosController.php',
    'alunos.ver'             => 'controllers/AlunosController.php',
    'responsaveis'           => 'controllers/ResponsaveisController.php',
    'responsaveis.criar'     => 'controllers/ResponsaveisController.php',
    'responsaveis.salvar'    => 'controllers/ResponsaveisController.php',
    'responsaveis.editar'    => 'controllers/ResponsaveisController.php',
    'responsaveis.atualizar' => 'controllers/ResponsaveisController.php',
    'responsaveis.excluir'   => 'controllers/ResponsaveisController.php',
    'academias'              => 'controllers/AcademiasController.php',
    'academias.criar'        => 'controllers/AcademiasController.php',
    'academias.salvar'       => 'controllers/AcademiasController.php',
    'academias.editar'       => 'controllers/AcademiasController.php',
    'academias.atualizar'    => 'controllers/AcademiasController.php',
    'academias.excluir'      => 'controllers/AcademiasController.php',
    'financeiro'             => 'controllers/FinanceiroController.php',
    'financeiro.lancar'      => 'controllers/FinanceiroController.php',
    'financeiro.salvar'      => 'controllers/FinanceiroController.php',
    'financeiro.pagar'       => 'controllers/FinanceiroController.php',
    'financeiro.excluir'     => 'controllers/FinanceiroController.php',
    'financeiro.cancelar'    => 'controllers/FinanceiroController.php',
    'financeiro.gerar'       => 'controllers/FinanceiroController.php',
    'exames'                 => 'controllers/ExamesController.php',
    'exames.criar'           => 'controllers/ExamesController.php',
    'exames.salvar'          => 'controllers/ExamesController.php',
    'exames.aprovar'         => 'controllers/ExamesController.php',
    'campeonatos'            => 'controllers/CampeonatosController.php',
    'campeonatos.criar'      => 'controllers/CampeonatosController.php',
    'campeonatos.salvar'     => 'controllers/CampeonatosController.php',
    'campeonatos.editar'     => 'controllers/CampeonatosController.php',
    'campeonatos.atualizar'  => 'controllers/CampeonatosController.php',
    'importacao'             => 'controllers/ImportacaoController.php',
    'importacao.upload'      => 'controllers/ImportacaoController.php',
    'exemplos'               => 'controllers/ExemplosController.php',
    'usuarios'               => 'controllers/UsuariosController.php',
    'usuarios.criar'         => 'controllers/UsuariosController.php',
    'usuarios.salvar'        => 'controllers/UsuariosController.php',
    'usuarios.editar'        => 'controllers/UsuariosController.php',
    'usuarios.atualizar'     => 'controllers/UsuariosController.php',
    'usuarios.excluir'       => 'controllers/UsuariosController.php',
    'perfil'                 => 'controllers/PerfilController.php',
    'perfil.senha'           => 'controllers/PerfilController.php',
    'faixas'                 => 'controllers/FaixasController.php',
    'faixas.salvar'          => 'controllers/FaixasController.php',
    'faixas.atualizar'       => 'controllers/FaixasController.php',
    'faixas.excluir'         => 'controllers/FaixasController.php',
    'faixas.reordenar'       => 'controllers/FaixasController.php',
    'responsaveis.vincular'  => 'controllers/ResponsaveisController.php',
    'responsaveis.desvincular' => 'controllers/ResponsaveisController.php',
    'configuracoes'           => 'controllers/ConfiguracoesController.php',
    'configuracoes.salvar'    => 'controllers/ConfiguracoesController.php',
];

$file = isset($routes[$page]) ? $routes[$page] : null;
if ($file && file_exists(__DIR__ . '/' . $file)) {
    require_once __DIR__ . '/' . $file;
} else {
    flashSet('danger', 'Pagina nao encontrada.');
    redirect('dashboard');
}

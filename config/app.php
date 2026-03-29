<?php


if (!defined('BASE_URL')) {
    $sd = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/'));
    define('BASE_URL', rtrim($sd, '/'));
}
if (!defined('APP_URL')) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    define('APP_URL', $scheme . '://' . $host . BASE_URL);
}

define('APP_NAME', 'Gestao de Academias');
date_default_timezone_set('America/Sao_Paulo');
define('FAIXAS', ['branca','cinza','azul','amarela','laranja','verde','roxa','marrom','preta']);
define('FORMAS_PAGAMENTO', ['Pix','Dinheiro','Cartao de Credito','Cartao de Debito','Boleto','Transferencia']);
define('TURNOS', ['M'=>'Manha','T'=>'Tarde','N'=>'Noite','MT'=>'Manha/Tarde']);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function redirect($rota) {
    if (ob_get_level()) ob_end_clean();
    if (strpos($rota, 'http') === 0) { header('Location: ' . $rota); exit; }
    $rota = ltrim($rota, '/');
    if (strpos($rota, '&') !== false) {
        $parts = explode('&', $rota, 2);
        $url   = APP_URL . '/index.php?page=' . $parts[0] . '&' . $parts[1];
    } else {
        $url = APP_URL . '/index.php?page=' . $rota;
    }
    header('Location: ' . $url);
    exit;
}

function isLoggedIn()  { return !empty($_SESSION['usuario_id']); }
function requireLogin() { if (!isLoggedIn()) redirect('login'); }
function isAdmin()     { return (isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '') === 'admin'; }
function requireAdmin() { requireLogin(); if (!isAdmin()) redirect('dashboard'); }
function h($s)         { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function flashSet($t,$m) { $_SESSION['flash'] = ['tipo'=>$t,'msg'=>$m]; }
function flashGet() {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
}
function formatMoeda($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
function formatData($d) {
    if (!$d || $d === '0000-00-00') return '-';
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    return $dt ? $dt->format('d/m/Y') : '-';
}
function mesReferencia($mes) {
    $meses = ['Janeiro','Fevereiro','Marco','Abril','Maio','Junho',
              'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $p = explode('-', $mes);
    $y = isset($p[0]) ? $p[0] : '';
    $m = isset($p[1]) ? (int)$p[1] : 0;
    return (($m>=1&&$m<=12) ? $meses[$m-1] : $mes) . '/' . $y;
}
function getAcademiaFiltro() {
    if (isAdmin()) return null;
    return isset($_SESSION['academia_id']) ? (int)$_SESSION['academia_id'] : null;
}

// Perfis do sistema
define('PERFIS', [
    'admin'      => 'Administrador',
    'financeiro' => 'Financeiro',
    'usuario'    => 'Usuario',
]);

function isFinanceiro() {
    $p = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
    return $p === 'financeiro' || $p === 'admin';
}

function getPerfil() {
    return isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
}

// Verifica se o usuario pode acessar determinada secao
function canAccess($secao) {
    $perfil = getPerfil();
    if ($perfil === 'admin') return true;
    $permissoes = [
        'financeiro' => ['dashboard','financeiro','alunos','responsaveis','exames','campeonatos'],
        'usuario'    => ['dashboard','alunos','responsaveis','exames','campeonatos'],
    ];
    $permitidas = isset($permissoes[$perfil]) ? $permissoes[$perfil] : [];
    foreach ($permitidas as $p) {
        if (strpos($secao, $p) === 0) return true;
    }
    return false;
}

function isColorDark($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) return false;
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));
    return (0.299*$r + 0.587*$g + 0.114*$b) < 140;
}

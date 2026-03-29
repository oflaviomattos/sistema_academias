<?php
// ============================================================
// DIAGNÓSTICO — acesse: /projetos/sistema_academias/debug.php
// DELETE este arquivo após resolver o problema!
// ============================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Version</h2>";
echo phpversion();

echo "<h2>Extensões carregadas</h2>";
echo implode(', ', ['pdo' => extension_loaded('pdo') ? '✅ PDO' : '❌ PDO',
                    'pdo_mysql' => extension_loaded('pdo_mysql') ? '✅ pdo_mysql' : '❌ pdo_mysql',
                    'session'   => extension_loaded('session')   ? '✅ session'   : '❌ session']);

echo "<h2>SCRIPT_NAME / REQUEST_URI</h2>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "<br>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "<br>";

echo "<h2>dirname(SCRIPT_NAME)</h2>";
$scriptDir = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : ''));
echo "Result: " . $scriptDir . "<br>";
echo "BASE_URL seria: " . rtrim($scriptDir, '/') . "<br>";

echo "<h2>Teste de conexão PDO</h2>";
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=hgin9424_gestao_academias;charset=utf8mb4',
        'hgin9424_gestao_academias',
        'EjA&}HYsdHiB',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Conexão OK<br>";
    $v = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "MySQL version: " . $v . "<br>";

    echo "<h2>Tabelas existentes</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $tables) ?: 'Nenhuma tabela encontrada';

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}

echo "<h2>Teste de session</h2>";
session_start();
$_SESSION['test'] = 'ok';
echo "Session ID: " . session_id() . "<br>";
echo "Session test: " . ($_SESSION['test'] ?? 'falhou') . "<br>";

echo "<h2>Arquivos críticos existem?</h2>";
$base = __DIR__;
$files = [
    'index.php',
    'config/app.php',
    'config/database.php',
    'models/AlunoModel.php',
    'models/MensalidadeModel.php',
    'models/OutrosModels.php',
    'controllers/AuthController.php',
    'controllers/DashboardController.php',
    'views/auth/login.php',
    'views/dashboard/index.php',
    'views/layouts/header.php',
];
foreach ($files as $f) {
    $exists = file_exists($base . '/' . $f);
    echo ($exists ? '✅' : '❌') . ' ' . $f . '<br>';
}

echo "<h2>Teste de include do app.php</h2>";
try {
    require_once __DIR__ . '/config/app.php';
    echo "✅ app.php carregado OK<br>";
    echo "BASE_URL definido: " . BASE_URL . "<br>";
} catch (Throwable $e) {
    echo "❌ Erro em app.php: " . $e->getMessage() . " (linha " . $e->getLine() . ")<br>";
}

echo "<h2>Teste de include do database.php</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = getDB();
    echo "✅ database.php e getDB() OK<br>";
} catch (Throwable $e) {
    echo "❌ Erro: " . $e->getMessage() . " (linha " . $e->getLine() . ")<br>";
}

echo "<h2>Teste de carregamento dos Models</h2>";
foreach (glob(__DIR__ . '/models/*.php') as $model) {
    try {
        require_once $model;
        echo "✅ " . basename($model) . "<br>";
    } catch (Throwable $e) {
        echo "❌ " . basename($model) . ": " . $e->getMessage() . " linha " . $e->getLine() . "<br>";
    }
}

echo "<hr><p style='color:red'><strong>DELETE este arquivo após o diagnóstico!</strong></p>";

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$ok    = [];
$erros = [];

try {
    $db = getDB();

    // Verifica quais colunas a tabela alunos já tem
    $cols = $db->query("SHOW COLUMNS FROM alunos")->fetchAll(PDO::FETCH_COLUMN);
    $ok[] = "Colunas atuais em 'alunos': " . implode(', ', $cols);

    // Adiciona cada coluna faltante individualmente
    $adicionar = [
        'turno'       => "ALTER TABLE alunos ADD COLUMN turno ENUM('M','T','N','MT') NOT NULL DEFAULT 'M' AFTER data_nascimento",
        'contrato_ok' => "ALTER TABLE alunos ADD COLUMN contrato_ok TINYINT(1) NOT NULL DEFAULT 0 AFTER turno",
        'observacoes' => "ALTER TABLE alunos ADD COLUMN observacoes TEXT NULL AFTER responsavel_id",
    ];

    foreach ($adicionar as $col => $sql) {
        if (in_array($col, $cols)) {
            $ok[] = "Coluna '$col' já existe — ignorada";
        } else {
            try {
                $db->exec($sql);
                $ok[] = "Coluna '$col' adicionada com sucesso ✅";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false) {
                    $ok[] = "Coluna '$col' já existe — ignorada";
                } else {
                    $erros[] = "Erro ao adicionar '$col': " . $e->getMessage();
                }
            }
        }
    }

    // Confirma o estado final
    $colsNow = $db->query("SHOW COLUMNS FROM alunos")->fetchAll(PDO::FETCH_COLUMN);
    $ok[] = "Colunas finais: " . implode(', ', $colsNow);

} catch (Exception $e) {
    $erros[] = "Erro: " . $e->getMessage();
}

ob_end_clean();
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><title>Migração</title>
<style>
body{font-family:system-ui,sans-serif;max-width:800px;margin:40px auto;padding:20px;background:#f8fafc}
h1{color:#1e293b;margin-bottom:20px}
.ok {background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:9px 16px;border-radius:6px;margin:4px 0;font-size:13.5px}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:9px 16px;border-radius:6px;margin:4px 0;font-size:13.5px}
.warn{background:#fef9c3;border:1px solid #fde68a;padding:14px;border-radius:6px;margin-top:20px;font-size:14px}
a.btn{display:inline-block;background:#16a34a;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:20px}
</style>
</head>
<body>
<h1>🔧 Migração — Colunas faltantes</h1>
<?php foreach ($ok    as $m): ?><div class="ok" >✅ <?php echo htmlspecialchars($m); ?></div><?php endforeach; ?>
<?php foreach ($erros as $e): ?><div class="err">❌ <?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
<?php if (empty($erros)): ?>
<a class="btn" href="<?php echo APP_URL; ?>/index.php?page=importacao">→ Ir para Importação</a>
<?php endif; ?>
<div class="warn">⚠️ <strong>Delete o migrar.php após executar!</strong></div>
</body>
</html>

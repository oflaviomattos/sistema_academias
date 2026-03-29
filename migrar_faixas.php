<?php
ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
$ok=[]; $erros=[];
try {
    $db = getDB();
    $db->exec("CREATE TABLE IF NOT EXISTS faixas (
      id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
      nome     VARCHAR(60)  NOT NULL,
      cor_hex  VARCHAR(7)   NOT NULL DEFAULT '#cccccc',
      ordem    TINYINT(3)   NOT NULL DEFAULT 0,
      created_at TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uq_nome (nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = "Tabela 'faixas' criada/verificada";

    // Insere faixas padrão do judô
    $faixas = [
        ['branca',           '#ffffff', 1],
        ['branca/cinza',     '#e0e0e0', 2],
        ['cinza',            '#9ca3af', 3],
        ['cinza/azul',       '#93c5fd', 4],
        ['azul',             '#3b82f6', 5],
        ['azul/amarela',     '#fbbf24', 6],
        ['amarela',          '#eab308', 7],
        ['amarela/laranja',  '#fb923c', 8],
        ['laranja',          '#f97316', 9],
        ['laranja/verde',    '#86efac', 10],
        ['verde',            '#22c55e', 11],
        ['verde/roxa',       '#a855f7', 12],
        ['roxa',             '#9333ea', 13],
        ['marrom',           '#92400e', 14],
        ['preta',            '#1e293b', 15],
    ];
    $ins = $db->prepare("INSERT IGNORE INTO faixas (nome,cor_hex,ordem) VALUES(?,?,?)");
    foreach ($faixas as $f) { $ins->execute($f); }
    $total = $db->query("SELECT COUNT(*) FROM faixas")->fetchColumn();
    $ok[] = "Faixas no banco: $total";
} catch (Exception $e) { $erros[] = $e->getMessage(); }
ob_end_clean();
?><!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Migrar Faixas</title>
<style>body{font-family:system-ui;max-width:700px;margin:40px auto;padding:20px;background:#f8fafc}
.ok{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:9px 16px;border-radius:6px;margin:4px 0}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:9px 16px;border-radius:6px;margin:4px 0}
.warn{background:#fef9c3;border:1px solid #fde68a;padding:14px;border-radius:6px;margin-top:20px}
a.btn{display:inline-block;background:#16a34a;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:16px}</style>
</head><body>
<h1>🎽 Migrar Faixas</h1>
<?php foreach($ok as $m): ?><div class="ok">✅ <?= htmlspecialchars($m) ?></div><?php endforeach; ?>
<?php foreach($erros as $e): ?><div class="err">❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<?php if(empty($erros)): ?>
<a class="btn" href="<?= APP_URL ?>/index.php?page=faixas">→ Gerenciar Faixas</a>
<?php endif; ?>
<div class="warn">⚠️ <strong>Delete este arquivo após executar!</strong></div>
</body></html>

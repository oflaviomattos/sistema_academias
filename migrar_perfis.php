<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$ok = []; $erros = [];

try {
    $db = getDB();

    // Verifica o ENUM atual da coluna perfil
    $col = $db->query("SHOW COLUMNS FROM usuarios LIKE 'perfil'")->fetch();
    $ok[] = "Coluna perfil atual: " . ($col['Type'] ?? 'nao encontrada');

    // Altera o ENUM para incluir 'financeiro'
    $db->exec("ALTER TABLE usuarios MODIFY COLUMN perfil ENUM('admin','financeiro','usuario') NOT NULL DEFAULT 'usuario'");
    $ok[] = "ENUM atualizado para: admin, financeiro, usuario";

    // Confirma
    $col2 = $db->query("SHOW COLUMNS FROM usuarios LIKE 'perfil'")->fetch();
    $ok[] = "Coluna perfil agora: " . ($col2['Type'] ?? '');

    // Lista usuarios existentes
    $total = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $ok[] = "Total de usuarios no banco: $total";

} catch (Exception $e) {
    $erros[] = $e->getMessage();
}

ob_end_clean();
?><!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Migrar Perfis</title>
<style>body{font-family:system-ui;max-width:700px;margin:40px auto;padding:20px;background:#f8fafc}
.ok{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:9px 16px;border-radius:6px;margin:4px 0}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:9px 16px;border-radius:6px;margin:4px 0}
.warn{background:#fef9c3;border:1px solid #fde68a;padding:14px;border-radius:6px;margin-top:20px}
a.btn{display:inline-block;background:#16a34a;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:16px}</style>
</head><body>
<h1>🔧 Migrar Perfis de Usuarios</h1>
<?php foreach($ok    as $m): ?><div class="ok">✅ <?= htmlspecialchars($m) ?></div><?php endforeach; ?>
<?php foreach($erros as $e): ?><div class="err">❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<?php if (empty($erros)): ?>
<a class="btn" href="<?= APP_URL ?>/index.php?page=usuarios">→ Ir para Usuarios</a>
<?php endif; ?>
<div class="warn">⚠️ <strong>Delete este arquivo apos executar!</strong></div>
</body></html>

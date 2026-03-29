<?php
ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
$ok=[]; $erros=[];
try {
    $db = getDB();
    $db->exec("CREATE TABLE IF NOT EXISTS configuracoes (
      chave      VARCHAR(80)  NOT NULL,
      valor      TEXT         NULL,
      updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (chave)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ok[] = "Tabela 'configuracoes' criada/verificada";

    // Insere mensagem padrão do professor
    $msg = "Bom dia familia!\nEstou encaminhando este lembrete referente a parcela do Judo ({academia}) que venceu {vencimento} no valor de {valor}.\nPIX: 79996480481 (Marcado Pago). Solicito que envie o comprovante para o controle. Qualquer duvida estou a disposicao.\nGrato!\nProf. Marcus Uilson Correa";

    $stmt = $db->prepare("INSERT IGNORE INTO configuracoes (chave, valor) VALUES ('mensagem_cobranca', ?)");
    $stmt->execute([$msg]);
    $ok[] = "Mensagem padrao inserida";

    $total = $db->query("SELECT COUNT(*) FROM configuracoes")->fetchColumn();
    $ok[] = "Total de configuracoes: $total";
} catch (Exception $e) { $erros[] = $e->getMessage(); }
ob_end_clean();
?><!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Migrar Configuracoes</title>
<style>body{font-family:system-ui;max-width:700px;margin:40px auto;padding:20px;background:#f8fafc}
.ok{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:9px 16px;border-radius:6px;margin:4px 0}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:9px 16px;border-radius:6px;margin:4px 0}
.warn{background:#fef9c3;border:1px solid #fde68a;padding:14px;border-radius:6px;margin-top:20px}
a.btn{display:inline-block;background:#16a34a;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:16px}</style>
</head><body>
<h1>⚙️ Migrar Configuracoes</h1>
<?php foreach($ok as $m): ?><div class="ok">✅ <?= htmlspecialchars($m) ?></div><?php endforeach; ?>
<?php foreach($erros as $e): ?><div class="err">❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<?php if(empty($erros)): ?>
<a class="btn" href="<?= APP_URL ?>/index.php?page=configuracoes">→ Editar Mensagem de Cobranca</a>
<?php endif; ?>
<div class="warn">⚠️ <strong>Delete este arquivo apos executar!</strong></div>
</body></html>

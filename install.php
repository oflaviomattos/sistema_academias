<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$erros = [];
$ok    = [];

// Conexão
try {
    $db = getDB();
    $ok[] = 'Conexao OK — MySQL ' . $db->query('SELECT VERSION()')->fetchColumn();
} catch (Exception $e) {
    $erros[] = 'Falha na conexao: ' . $e->getMessage();
    goto fim;
}

// Cria cada tabela individualmente (sem depender do schema.sql)
$tabelas = [

'academias' => "CREATE TABLE IF NOT EXISTS academias (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nome        VARCHAR(120)  NOT NULL,
  cidade      VARCHAR(80)   NULL,
  responsavel VARCHAR(120)  NULL,
  ativo       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'usuarios' => "CREATE TABLE IF NOT EXISTS usuarios (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(120) NOT NULL,
  email        VARCHAR(150) NOT NULL,
  senha_hash   VARCHAR(255) NOT NULL,
  perfil       ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
  academia_id  INT UNSIGNED NULL,
  ativo        TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'responsaveis' => "CREATE TABLE IF NOT EXISTS responsaveis (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(120) NOT NULL,
  telefone   VARCHAR(20)  NOT NULL,
  email      VARCHAR(150) NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'alunos' => "CREATE TABLE IF NOT EXISTS alunos (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome_completo   VARCHAR(150) NOT NULL,
  data_nascimento DATE         NULL,
  turno           ENUM('M','T','N','MT') NOT NULL DEFAULT 'M',
  contrato_ok     TINYINT(1)   NOT NULL DEFAULT 0,
  faixa           VARCHAR(30)  NOT NULL DEFAULT 'branca',
  serie_nivel     TINYINT(1)   NULL,
  tamanho         VARCHAR(10)  NULL,
  status          ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
  data_entrada    DATE         NOT NULL,
  academia_id     INT UNSIGNED NOT NULL,
  responsavel_id  INT UNSIGNED NULL,
  observacoes     TEXT         NULL,
  created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_alunos_academia (academia_id),
  KEY idx_alunos_status   (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'contratos' => "CREATE TABLE IF NOT EXISTS contratos (
  id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  aluno_id    INT UNSIGNED   NOT NULL,
  tipo_plano  VARCHAR(60)    NOT NULL DEFAULT 'mensal',
  valor       DECIMAL(10,2)  NOT NULL,
  data_inicio DATE           NOT NULL,
  data_fim    DATE           NULL,
  status      ENUM('ativo','encerrado','suspenso') NOT NULL DEFAULT 'ativo',
  observacoes TEXT           NULL,
  created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contratos_aluno (aluno_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'mensalidades' => "CREATE TABLE IF NOT EXISTS mensalidades (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  aluno_id        INT UNSIGNED  NOT NULL,
  mes_referencia  CHAR(7)       NOT NULL,
  valor           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status          ENUM('pago','pendente','atrasado','integral') NOT NULL DEFAULT 'pendente',
  forma_pagamento VARCHAR(40)   NULL,
  data_vencimento DATE          NOT NULL,
  data_pagamento  DATE          NULL,
  observacoes     VARCHAR(255)  NULL,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_aluno_mes (aluno_id, mes_referencia),
  KEY idx_mensalidades_status     (status),
  KEY idx_mensalidades_vencimento (data_vencimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'exames_faixa' => "CREATE TABLE IF NOT EXISTS exames_faixa (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  aluno_id    INT UNSIGNED NOT NULL,
  faixa_atual VARCHAR(30)  NOT NULL,
  nova_faixa  VARCHAR(30)  NOT NULL,
  data_exame  DATE         NOT NULL,
  status      ENUM('aprovado','reprovado','pendente') NOT NULL DEFAULT 'pendente',
  observacoes TEXT         NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_exames_aluno (aluno_id),
  KEY idx_exames_data  (data_exame)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'campeonatos' => "CREATE TABLE IF NOT EXISTS campeonatos (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(150) NOT NULL,
  data       DATE         NOT NULL,
  local      VARCHAR(150) NULL,
  descricao  TEXT         NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'campeonato_aluno' => "CREATE TABLE IF NOT EXISTS campeonato_aluno (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  campeonato_id INT UNSIGNED NOT NULL,
  aluno_id      INT UNSIGNED NOT NULL,
  resultado     VARCHAR(100) NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_camp_aluno (campeonato_id, aluno_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

];

// Adiciona FKs separadas (sem IF NOT EXISTS — ignora erro se já existe)
$fks = [
    "ALTER TABLE usuarios    ADD CONSTRAINT fk_usr_ac  FOREIGN KEY (academia_id)  REFERENCES academias(id) ON DELETE SET NULL ON UPDATE CASCADE",
    "ALTER TABLE alunos      ADD CONSTRAINT fk_al_ac   FOREIGN KEY (academia_id)  REFERENCES academias(id) ON DELETE RESTRICT ON UPDATE CASCADE",
    "ALTER TABLE alunos      ADD CONSTRAINT fk_al_resp FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id) ON DELETE SET NULL ON UPDATE CASCADE",
    "ALTER TABLE contratos   ADD CONSTRAINT fk_ct_al   FOREIGN KEY (aluno_id)     REFERENCES alunos(id) ON DELETE CASCADE ON UPDATE CASCADE",
    "ALTER TABLE mensalidades ADD CONSTRAINT fk_ms_al  FOREIGN KEY (aluno_id)     REFERENCES alunos(id) ON DELETE CASCADE ON UPDATE CASCADE",
    "ALTER TABLE exames_faixa ADD CONSTRAINT fk_ex_al  FOREIGN KEY (aluno_id)     REFERENCES alunos(id) ON DELETE CASCADE ON UPDATE CASCADE",
    "ALTER TABLE campeonato_aluno ADD CONSTRAINT fk_ca_camp FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id) ON DELETE CASCADE ON UPDATE CASCADE",
    "ALTER TABLE campeonato_aluno ADD CONSTRAINT fk_ca_al   FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE ON UPDATE CASCADE",
];

// Executa criação das tabelas
foreach ($tabelas as $nome => $sql) {
    try {
        $db->exec($sql);
        $ok[] = "Tabela <strong>$nome</strong> OK";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            $ok[] = "Tabela <strong>$nome</strong> ja existia";
        } else {
            $erros[] = "Tabela $nome: " . $e->getMessage();
        }
    }
}

// Executa FKs (ignora erros de FK duplicada)
foreach ($fks as $fk) {
    try {
        $db->exec($fk);
    } catch (PDOException $e) {
        // Ignora FK duplicada silenciosamente
    }
}
$ok[] = "Foreign keys processadas";

// Insere academia e admin padrão
try {
    $db->exec("INSERT IGNORE INTO academias (id, nome, cidade, responsavel) VALUES (1, 'Academia Principal', 'Sua Cidade', 'Administrador')");
    $hash = password_hash('Admin@2025', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT IGNORE INTO usuarios (nome, email, senha_hash, perfil, academia_id) VALUES (?, ?, ?, 'admin', NULL)");
    $stmt->execute(['Administrador', 'admin@academia.com', $hash]);
    $total = $db->query("SELECT COUNT(*) FROM usuarios WHERE perfil='admin'")->fetchColumn();
    $ok[] = "Admin OK: $total usuario(s) admin";
} catch (PDOException $e) {
    $erros[] = "Admin: " . $e->getMessage();
}

// Lista tabelas finais
$tabsCriadas = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$ok[] = "Total de tabelas: " . count($tabsCriadas) . " — " . implode(', ', $tabsCriadas);
$ok[] = "APP_URL: " . APP_URL;

fim:
ob_end_clean();
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><title>Instalacao</title>
<style>
body{font-family:system-ui,sans-serif;max-width:820px;margin:40px auto;padding:20px;background:#f8fafc}
h1{color:#1e293b;margin-bottom:20px}
.ok {background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:9px 16px;border-radius:6px;margin:4px 0;font-size:13.5px}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:9px 16px;border-radius:6px;margin:4px 0;font-size:13.5px}
.cred{background:#fff;border:1px solid #e2e8f0;padding:16px;border-radius:8px;margin-top:20px;font-size:14px;line-height:2}
.warn{background:#fef9c3;border:1px solid #fde68a;padding:14px;border-radius:6px;margin-top:20px;font-size:14px}
a.btn{display:inline-block;background:#16a34a;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600;margin-top:20px;font-size:15px}
code{background:#f1f5f9;padding:2px 7px;border-radius:4px;font-size:13px}
</style>
</head>
<body>
<h1>🥋 Instalacao — <?php echo APP_NAME; ?></h1>
<?php foreach ($ok   as $m): ?><div class="ok" >✅ <?php echo $m; ?></div><?php endforeach; ?>
<?php foreach ($erros as $e): ?><div class="err">❌ <?php echo $e; ?></div><?php endforeach; ?>
<?php if (empty($erros)): ?>
<div class="cred">
  <strong>Login de acesso:</strong><br>
  📧 E-mail: <code>admin@academia.com</code><br>
  🔑 Senha: <code>Admin@2025</code>
</div>
<a class="btn" href="<?php echo APP_URL; ?>/index.php?page=login">→ Ir para o Login</a>
<?php endif; ?>
<div class="warn">⚠️ <strong>Delete o install.php após instalar!</strong></div>
</body>
</html>

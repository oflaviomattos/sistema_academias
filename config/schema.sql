-- ============================================================
-- SISTEMA DE GESTÃO DE ACADEMIAS — DDL COMPLETO
-- Banco: hgin9424_gestao_academias | MySQL 5.7+
-- Execute este script para criar todas as tabelas
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. ACADEMIAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS academias (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nome        VARCHAR(120)  NOT NULL,
  cidade      VARCHAR(80)   NULL,
  responsavel VARCHAR(120)  NULL,
  ativo       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. USUÁRIOS DO SISTEMA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(120) NOT NULL,
  email        VARCHAR(150) NOT NULL,
  senha_hash   VARCHAR(255) NOT NULL,
  perfil       ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
  academia_id  INT UNSIGNED NULL,
  ativo        TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email),
  CONSTRAINT fk_usuarios_academia
    FOREIGN KEY (academia_id) REFERENCES academias(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário admin padrão (senha: Admin@2025 — troque imediatamente!)
INSERT IGNORE INTO academias (nome, cidade, responsavel) VALUES ('Academia Principal', 'Sua Cidade', 'Administrador');
INSERT IGNORE INTO usuarios (nome, email, senha_hash, perfil, academia_id)
VALUES ('Administrador', 'admin@academia.com',
        '$2y$10$4AGcs6qF9qyZeX13E4MtsuoSM/CgzrGlPjn5XI7Em6K/c0QtZJzPy', -- Admin@2025
        'admin', NULL);

-- ------------------------------------------------------------
-- 3. RESPONSÁVEIS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS responsaveis (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(120) NOT NULL,
  telefone   VARCHAR(20)  NOT NULL,
  email      VARCHAR(150) NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. ALUNOS
-- Inclui turno (manha/tarde) e campo contrato conforme planilha
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS alunos (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome_completo  VARCHAR(150) NOT NULL,
  data_nascimento DATE         NULL,
  turno          ENUM('M','T','N','MT') NOT NULL DEFAULT 'M',
  contrato_ok    TINYINT(1)   NOT NULL DEFAULT 0,  -- coluna "contrato" da planilha
  faixa          VARCHAR(30)  NOT NULL DEFAULT 'branca',
  serie_nivel    TINYINT(1)   NULL,                -- # da série (1,2,3,4,5...)
  tamanho        VARCHAR(10)  NULL,
  status         ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
  data_entrada   DATE         NOT NULL,
  academia_id    INT UNSIGNED NOT NULL,
  responsavel_id INT UNSIGNED NULL,
  observacoes    TEXT         NULL,
  created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_alunos_academia
    FOREIGN KEY (academia_id) REFERENCES academias(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_alunos_responsavel
    FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  KEY idx_alunos_academia (academia_id),
  KEY idx_alunos_status   (status),
  KEY idx_alunos_faixa    (faixa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. CONTRATOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contratos (
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
  CONSTRAINT fk_contratos_aluno
    FOREIGN KEY (aluno_id) REFERENCES alunos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_contratos_aluno  (aluno_id),
  KEY idx_contratos_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. MENSALIDADES
-- mes_referencia = 'YYYY-MM' (ex: '2025-01')
-- Status: pago | pendente | atrasado | integral (bolsa integral)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensalidades (
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
  CONSTRAINT fk_mensalidades_aluno
    FOREIGN KEY (aluno_id) REFERENCES alunos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_mensalidades_status     (status),
  KEY idx_mensalidades_vencimento (data_vencimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. EXAMES DE FAIXA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS exames_faixa (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  aluno_id    INT UNSIGNED NOT NULL,
  faixa_atual VARCHAR(30)  NOT NULL,
  nova_faixa  VARCHAR(30)  NOT NULL,
  data_exame  DATE         NOT NULL,
  status      ENUM('aprovado','reprovado','pendente') NOT NULL DEFAULT 'pendente',
  observacoes TEXT         NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_exames_aluno
    FOREIGN KEY (aluno_id) REFERENCES alunos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_exames_aluno (aluno_id),
  KEY idx_exames_data  (data_exame)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. CAMPEONATOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS campeonatos (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(150) NOT NULL,
  data       DATE         NOT NULL,
  local      VARCHAR(150) NULL,
  descricao  TEXT         NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_campeonatos_data (data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. CAMPEONATO_ALUNO (pivot N:N)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS campeonato_aluno (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  campeonato_id INT UNSIGNED NOT NULL,
  aluno_id      INT UNSIGNED NOT NULL,
  resultado     VARCHAR(100) NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_camp_aluno (campeonato_id, aluno_id),
  CONSTRAINT fk_ca_campeonato
    FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ca_aluno
    FOREIGN KEY (aluno_id) REFERENCES alunos(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- FIM DO SCRIPT
SELECT 'Banco criado com sucesso!' AS status;

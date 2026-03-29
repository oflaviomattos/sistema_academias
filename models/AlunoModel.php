<?php
// ============================================================
// MODEL: Alunos
// ============================================================

class AlunoModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listar(array $filtros = []): array {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['academia_id'])) {
            $where[] = 'a.academia_id = :academia_id';
            $params['academia_id'] = $filtros['academia_id'];
        }
        if (!empty($filtros['status'])) {
            $where[] = 'a.status = :status';
            $params['status'] = $filtros['status'];
        }
        if (!empty($filtros['faixa'])) {
            $where[] = 'a.faixa = :faixa';
            $params['faixa'] = $filtros['faixa'];
        }
        if (!empty($filtros['serie'])) {
            $where[] = 'a.serie_nivel = :serie';
            $params['serie'] = $filtros['serie'];
        }
        if (!empty($filtros['busca'])) {
            $where[] = '(a.nome_completo LIKE :busca1 OR r.nome LIKE :busca2)';
            $params['busca1'] = '%' . $filtros['busca'] . '%';
            $params['busca2'] = '%' . $filtros['busca'] . '%';
        }

        $sql = "SELECT a.*, ac.nome AS academia_nome, r.nome AS responsavel_nome, r.telefone AS responsavel_telefone
                FROM alunos a
                LEFT JOIN academias ac ON ac.id = a.academia_id
                LEFT JOIN responsaveis r ON r.id = a.responsavel_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY a.serie_nivel ASC, a.nome_completo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT a.*, ac.nome AS academia_nome, r.nome AS responsavel_nome, r.telefone AS responsavel_telefone
             FROM alunos a
             LEFT JOIN academias ac ON ac.id = a.academia_id
             LEFT JOIN responsaveis r ON r.id = a.responsavel_id
             WHERE a.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function criar(array $dados): int {
        $stmt = $this->db->prepare(
            "INSERT INTO alunos (nome_completo, data_nascimento, turno, contrato_ok, faixa,
             serie_nivel, tamanho, status, data_entrada, academia_id, responsavel_id, observacoes)
             VALUES (:nome_completo, :data_nascimento, :turno, :contrato_ok, :faixa,
             :serie_nivel, :tamanho, :status, :data_entrada, :academia_id, :responsavel_id, :observacoes)"
        );
        $stmt->execute([
            'nome_completo'   => $dados['nome_completo'],
            'data_nascimento' => $dados['data_nascimento'] ?: null,
            'turno'           => $dados['turno'] ?? 'M',
            'contrato_ok'     => !empty($dados['contrato_ok']) ? 1 : 0,
            'faixa'           => $dados['faixa'] ?? 'branca',
            'serie_nivel'     => $dados['serie_nivel'] ?: null,
            'tamanho'         => $dados['tamanho'] ?: null,
            'status'          => $dados['status'] ?? 'ativo',
            'data_entrada'    => $dados['data_entrada'],
            'academia_id'     => $dados['academia_id'],
            'responsavel_id'  => $dados['responsavel_id'] ?: null,
            'observacoes'     => $dados['observacoes'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE alunos SET nome_completo=:nome_completo, data_nascimento=:data_nascimento,
             turno=:turno, contrato_ok=:contrato_ok, faixa=:faixa, serie_nivel=:serie_nivel,
             tamanho=:tamanho, status=:status, data_entrada=:data_entrada,
             academia_id=:academia_id, responsavel_id=:responsavel_id, observacoes=:observacoes
             WHERE id=:id"
        );
        return $stmt->execute([
            'id'              => $id,
            'nome_completo'   => $dados['nome_completo'],
            'data_nascimento' => $dados['data_nascimento'] ?: null,
            'turno'           => $dados['turno'] ?? 'M',
            'contrato_ok'     => !empty($dados['contrato_ok']) ? 1 : 0,
            'faixa'           => $dados['faixa'] ?? 'branca',
            'serie_nivel'     => $dados['serie_nivel'] ?: null,
            'tamanho'         => $dados['tamanho'] ?: null,
            'status'          => $dados['status'] ?? 'ativo',
            'data_entrada'    => $dados['data_entrada'],
            'academia_id'     => $dados['academia_id'],
            'responsavel_id'  => $dados['responsavel_id'] ?: null,
            'observacoes'     => $dados['observacoes'] ?: null,
        ]);
    }

    public function excluir(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM alunos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function contarPorStatus(string $status, ?int $academiaId = null): int {
        $sql = "SELECT COUNT(*) FROM alunos WHERE status = :status";
        $params = ['status' => $status];
        if ($academiaId) {
            $sql .= " AND academia_id = :academia_id";
            $params['academia_id'] = $academiaId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function contarAtivos(?int $academiaId = null): int {
        $sql = "SELECT COUNT(*) FROM alunos WHERE status='ativo'";
        $params = [];
        if ($academiaId) {
            $sql .= " AND academia_id=:a";
            $params['a'] = $academiaId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function listarParaSelect(?int $academiaId = null): array {
        $sql = "SELECT id, nome_completo, faixa, serie_nivel FROM alunos WHERE status='ativo'";
        $params = [];
        if ($academiaId) { $sql .= " AND academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " ORDER BY nome_completo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function atualizarFaixa(int $id, string $faixa): bool {
        $stmt = $this->db->prepare("UPDATE alunos SET faixa=:faixa WHERE id=:id");
        return $stmt->execute(['faixa' => $faixa, 'id' => $id]);
    }
}

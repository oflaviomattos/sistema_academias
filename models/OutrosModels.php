<?php
// ============================================================
// MODEL: Academias
// ============================================================
class AcademiaModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function listar(): array {
        return $this->db->query("SELECT * FROM academias ORDER BY nome")->fetchAll();
    }
    public function listarAtivas(): array {
        return $this->db->query("SELECT * FROM academias WHERE ativo=1 ORDER BY nome")->fetchAll();
    }
    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM academias WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch(); return $r ?: null;
    }
    public function criar(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO academias (nome,cidade,responsavel,ativo) VALUES(:nome,:cidade,:responsavel,:ativo)"
        );
        $stmt->execute(['nome'=>$d['nome'],'cidade'=>$d['cidade']??null,'responsavel'=>$d['responsavel']??null,'ativo'=>1]);
        return (int)$this->db->lastInsertId();
    }
    public function atualizar(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            "UPDATE academias SET nome=:nome,cidade=:cidade,responsavel=:responsavel,ativo=:ativo WHERE id=:id"
        );
        return $stmt->execute(['id'=>$id,'nome'=>$d['nome'],'cidade'=>$d['cidade']??null,'responsavel'=>$d['responsavel']??null,'ativo'=>$d['ativo']??1]);
    }
    public function excluir(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM academias WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }
}

// ============================================================
// MODEL: Responsáveis
// ============================================================
class ResponsavelModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function listar(string $busca = ''): array {
        $sql = "SELECT r.*, COUNT(a.id) as total_alunos FROM responsaveis r
                LEFT JOIN alunos a ON a.responsavel_id = r.id";
        $params = [];
        if ($busca) { $sql .= " WHERE r.nome LIKE :b1 OR r.telefone LIKE :b2"; $params['b1'] = "%$busca%"; $params['b2'] = "%$busca%"; }
        $sql .= " GROUP BY r.id ORDER BY r.nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarAlunos(int $responsavelId): array {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.nome_completo, a.faixa, a.serie_nivel, a.status, a.turno,
                    ac.nome AS academia_nome
             FROM alunos a
             LEFT JOIN academias ac ON ac.id = a.academia_id
             WHERE a.responsavel_id = :id
             ORDER BY a.nome_completo ASC"
        );
        $stmt->execute(['id' => $responsavelId]);
        return $stmt->fetchAll();
    }
    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM responsaveis WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $r = $stmt->fetch(); return $r ?: null;
    }
    public function criar(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO responsaveis (nome,telefone,email) VALUES(:nome,:telefone,:email)"
        );
        $stmt->execute(['nome'=>$d['nome'],'telefone'=>$d['telefone'],'email'=>$d['email']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function atualizar(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            "UPDATE responsaveis SET nome=:nome,telefone=:telefone,email=:email WHERE id=:id"
        );
        return $stmt->execute(['id'=>$id,'nome'=>$d['nome'],'telefone'=>$d['telefone'],'email'=>$d['email']??null]);
    }
    public function excluir(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM responsaveis WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }
    public function listarParaSelect(): array {
        return $this->db->query("SELECT id,nome,telefone FROM responsaveis ORDER BY nome")->fetchAll();
    }
}

// ============================================================
// MODEL: Exames de Faixa
// ============================================================
class ExameModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function listar(?int $academiaId = null): array {
        $sql = "SELECT e.*, al.nome_completo, al.faixa AS faixa_atual_aluno, ac.nome AS academia_nome
                FROM exames_faixa e
                JOIN alunos al ON al.id = e.aluno_id
                JOIN academias ac ON ac.id = al.academia_id
                WHERE 1=1";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " ORDER BY e.data_exame DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT e.*, al.nome_completo, al.academia_id FROM exames_faixa e
             JOIN alunos al ON al.id = e.aluno_id WHERE e.id=:id"
        );
        $stmt->execute(['id'=>$id]);
        $r = $stmt->fetch(); return $r ?: null;
    }
    public function criar(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO exames_faixa (aluno_id,faixa_atual,nova_faixa,data_exame,status,observacoes)
             VALUES(:aluno_id,:faixa_atual,:nova_faixa,:data_exame,:status,:observacoes)"
        );
        $stmt->execute([
            'aluno_id'   => $d['aluno_id'],
            'faixa_atual'=> $d['faixa_atual'],
            'nova_faixa' => $d['nova_faixa'],
            'data_exame' => $d['data_exame'],
            'status'     => $d['status'] ?? 'pendente',
            'observacoes'=> $d['observacoes'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }
    public function aprovar(int $id): bool {
        // Atualiza exame e faixa do aluno
        $exame = $this->buscarPorId($id);
        if (!$exame) return false;
        $this->db->prepare("UPDATE exames_faixa SET status='aprovado' WHERE id=:id")->execute(['id'=>$id]);
        $model = new AlunoModel();
        $model->atualizarFaixa((int)$exame['aluno_id'], $exame['nova_faixa']);
        return true;
    }
    public function proximosExames(?int $academiaId = null): array {
        $sql = "SELECT e.*, al.nome_completo FROM exames_faixa e
                JOIN alunos al ON al.id=e.aluno_id
                WHERE e.status='pendente' AND e.data_exame >= CURDATE()";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " ORDER BY e.data_exame ASC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

// ============================================================
// MODEL: Campeonatos
// ============================================================
class CampeonatoModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function listar(): array {
        return $this->db->query(
            "SELECT c.*, COUNT(ca.id) AS total_inscritos FROM campeonatos c
             LEFT JOIN campeonato_aluno ca ON ca.campeonato_id=c.id
             GROUP BY c.id ORDER BY c.data DESC"
        )->fetchAll();
    }
    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM campeonatos WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $r = $stmt->fetch(); return $r ?: null;
    }
    public function buscarInscritos(int $campId): array {
        $stmt = $this->db->prepare(
            "SELECT ca.*, al.nome_completo, al.faixa FROM campeonato_aluno ca
             JOIN alunos al ON al.id=ca.aluno_id WHERE ca.campeonato_id=:c ORDER BY al.nome_completo"
        );
        $stmt->execute(['c'=>$campId]);
        return $stmt->fetchAll();
    }
    public function criar(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO campeonatos (nome,data,local,descricao) VALUES(:nome,:data,:local,:descricao)"
        );
        $stmt->execute(['nome'=>$d['nome'],'data'=>$d['data'],'local'=>$d['local']??null,'descricao'=>$d['descricao']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function atualizar(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            "UPDATE campeonatos SET nome=:nome,data=:data,local=:local,descricao=:descricao WHERE id=:id"
        );
        return $stmt->execute(['id'=>$id,'nome'=>$d['nome'],'data'=>$d['data'],'local'=>$d['local']??null,'descricao'=>$d['descricao']??null]);
    }
    public function inscreverAluno(int $campId, int $alunoId): bool {
        try {
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO campeonato_aluno (campeonato_id,aluno_id) VALUES(:c,:a)"
            );
            return $stmt->execute(['c'=>$campId,'a'=>$alunoId]);
        } catch (PDOException $e) { return false; }
    }
    public function proximosCampeonatos(?int $academiaId = null): array {
        return $this->db->query(
            "SELECT * FROM campeonatos WHERE data >= CURDATE() ORDER BY data ASC LIMIT 5"
        )->fetchAll();
    }
}

// ============================================================
// MODEL: Faixas configuráveis
// ============================================================
class FaixaModel {
    private $db;
    public function __construct() { $this->db = getDB(); }

    public function listar(): array {
        return $this->db->query(
            "SELECT * FROM faixas ORDER BY ordem ASC"
        )->fetchAll();
    }

    public function buscarPorId($id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM faixas WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        $r = $stmt->fetch(); return $r ?: null;
    }

    public function criar($dados): int {
        $stmt = $this->db->prepare(
            "INSERT INTO faixas (nome, cor_hex, ordem) VALUES (:nome, :cor, :ordem)"
        );
        $stmt->execute([
            'nome'  => $dados['nome'],
            'cor'   => $dados['cor_hex'],
            'ordem' => (int)$dados['ordem'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function atualizar($id, $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE faixas SET nome=:nome, cor_hex=:cor, ordem=:ordem WHERE id=:id"
        );
        return $stmt->execute([
            'id'    => $id,
            'nome'  => $dados['nome'],
            'cor'   => $dados['cor_hex'],
            'ordem' => (int)$dados['ordem'],
        ]);
    }

    public function excluir($id): bool {
        $stmt = $this->db->prepare("DELETE FROM faixas WHERE id=:id");
        return $stmt->execute(['id'=>$id]);
    }

    public function reordenar($ids): void {
        foreach ($ids as $ordem => $id) {
            $stmt = $this->db->prepare("UPDATE faixas SET ordem=:o WHERE id=:id");
            $stmt->execute(['o' => $ordem + 1, 'id' => (int)$id]);
        }
    }

    public function listarNomes(): array {
        $rows = $this->listar();
        return array_column($rows, 'nome');
    }
}

// ============================================================
// MODEL: Responsavel - vincular/desvincular alunos
// (Adicionado ao ResponsavelModel existente via função global)
// ============================================================
function vincularAlunoResponsavel($alunoId, $responsavelId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE alunos SET responsavel_id=:r WHERE id=:a");
    return $stmt->execute(['r' => $responsavelId ?: null, 'a' => $alunoId]);
}

function desvincularAlunoResponsavel($alunoId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE alunos SET responsavel_id=NULL WHERE id=:a");
    return $stmt->execute(['a' => $alunoId]);
}

// ============================================================
// MODEL: Configuracoes do sistema
// ============================================================
class ConfiguracaoModel {
    private $db;
    public function __construct() { $this->db = getDB(); }

    public function get($chave, $padrao = '') {
        try {
            $stmt = $this->db->prepare("SELECT valor FROM configuracoes WHERE chave=:c");
            $stmt->execute(['c' => $chave]);
            $r = $stmt->fetchColumn();
            return $r !== false ? $r : $padrao;
        } catch (Exception $e) { return $padrao; }
    }

    public function set($chave, $valor) {
        $stmt = $this->db->prepare(
            "INSERT INTO configuracoes (chave, valor) VALUES (:c, :v)
             ON DUPLICATE KEY UPDATE valor=:v2"
        );
        return $stmt->execute(['c'=>$chave, 'v'=>$valor, 'v2'=>$valor]);
    }
}

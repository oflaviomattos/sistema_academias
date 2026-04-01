<?php
// ============================================================
// MODEL: Mensalidades (Financeiro)
// ============================================================

class MensalidadeModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listar(array $filtros = []): array {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['academia_id'])) {
            $where[] = 'al.academia_id = :academia_id';
            $params['academia_id'] = $filtros['academia_id'];
        }
        if (!empty($filtros['status'])) {
            $where[] = 'm.status = :status';
            $params['status'] = $filtros['status'];
        }
        if (!empty($filtros['mes_referencia'])) {
            $where[] = 'm.mes_referencia = :mes';
            $params['mes'] = $filtros['mes_referencia'];
        }
        if (!empty($filtros['aluno_id'])) {
            $where[] = 'm.aluno_id = :aluno_id';
            $params['aluno_id'] = $filtros['aluno_id'];
        }
        if (!empty($filtros['busca'])) {
            $where[] = 'al.nome_completo LIKE :busca';
            $params['busca'] = '%' . $filtros['busca'] . '%';
        }

        $sql = "SELECT m.*, al.nome_completo, al.faixa, al.turno, al.bolsa_percentual,
                       ac.nome AS academia_nome
                FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                JOIN academias ac ON ac.id = al.academia_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY m.data_vencimento ASC, al.nome_completo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT m.*, al.nome_completo, al.academia_id
             FROM mensalidades m JOIN alunos al ON al.id = m.aluno_id
             WHERE m.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function buscarPorAlunoMes(int $alunoId, string $mes): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM mensalidades WHERE aluno_id=:a AND mes_referencia=:m"
        );
        $stmt->execute(['a' => $alunoId, 'm' => $mes]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function criar(array $dados): int {
        $stmt = $this->db->prepare(
            "INSERT INTO mensalidades (aluno_id, mes_referencia, valor, status,
             forma_pagamento, data_vencimento, data_pagamento, observacoes)
             VALUES (:aluno_id, :mes_referencia, :valor, :status,
             :forma_pagamento, :data_vencimento, :data_pagamento, :observacoes)"
        );
        $stmt->execute([
            'aluno_id'        => $dados['aluno_id'],
            'mes_referencia'  => $dados['mes_referencia'],
            'valor'           => $dados['valor'],
            'status'          => $dados['status'] ?? 'pendente',
            'forma_pagamento' => $dados['forma_pagamento'] ?: null,
            'data_vencimento' => $dados['data_vencimento'],
            'data_pagamento'  => $dados['data_pagamento'] ?: null,
            'observacoes'     => $dados['observacoes'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function registrarPagamento(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE mensalidades SET status='pago', forma_pagamento=:forma,
             data_pagamento=:data_pg, observacoes=:obs WHERE id=:id"
        );
        return $stmt->execute([
            'id'      => $id,
            'forma'   => $dados['forma_pagamento'],
            'data_pg' => $dados['data_pagamento'] ?? date('Y-m-d'),
            'obs'     => $dados['observacoes'] ?? null,
        ]);
    }

    public function cancelar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mensalidades WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE mensalidades SET valor=:valor, status=:status,
             forma_pagamento=:forma_pagamento, data_vencimento=:data_vencimento,
             data_pagamento=:data_pagamento, observacoes=:observacoes WHERE id=:id"
        );
        return $stmt->execute([
            'id'              => $id,
            'valor'           => $dados['valor'],
            'status'          => $dados['status'],
            'forma_pagamento' => $dados['forma_pagamento'] ?: null,
            'data_vencimento' => $dados['data_vencimento'],
            'data_pagamento'  => $dados['data_pagamento'] ?: null,
            'observacoes'     => $dados['observacoes'] ?: null,
        ]);
    }

    public function excluir(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mensalidades WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    // Calcula o N-ésimo dia útil de um mês/ano
    private function diaUtilMes(int $ano, int $mes, int $n): string {
        $dia = 1;
        $contador = 0;
        while ($contador < $n) {
            $data = mktime(0, 0, 0, $mes, $dia, $ano);
            $semana = date('w', $data); // 0=domingo, 6=sábado
            if ($semana != 0 && $semana != 6) {
                $contador++;
                if ($contador == $n) break;
            }
            $dia++;
        }
        return sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
    }

    // Atualiza automaticamente status para 'atrasado' após o 5º dia útil do mês seguinte
    public function atualizarAtrasados(): int {
        // Primeiro: reseta atrasados que ainda não deveriam estar (correção da lógica antiga)
        $stmt = $this->db->prepare("SELECT id, mes_referencia FROM mensalidades WHERE status='atrasado'");
        $stmt->execute();
        $atrasados = $stmt->fetchAll();

        foreach ($atrasados as $p) {
            $partes = explode('-', $p['mes_referencia']);
            $ano = (int)$partes[0];
            $mes = (int)$partes[1];
            $mesSeg = $mes + 1;
            $anoSeg = $ano;
            if ($mesSeg > 12) { $mesSeg = 1; $anoSeg++; }
            $limite = $this->diaUtilMes($anoSeg, $mesSeg, 5);
            $hoje = date('Y-m-d');
            if ($hoje <= $limite) {
                $upd = $this->db->prepare("UPDATE mensalidades SET status='pendente' WHERE id=:id");
                $upd->execute(['id' => $p['id']]);
            }
        }

        // Depois: marca como atrasado os que já passaram do 5º dia útil
        $stmt = $this->db->prepare("SELECT id, mes_referencia FROM mensalidades WHERE status='pendente'");
        $stmt->execute();
        $pendentes = $stmt->fetchAll();

        $hoje = date('Y-m-d');
        $atualizados = 0;

        foreach ($pendentes as $p) {
            $partes = explode('-', $p['mes_referencia']);
            $ano = (int)$partes[0];
            $mes = (int)$partes[1];

            $mesSeg = $mes + 1;
            $anoSeg = $ano;
            if ($mesSeg > 12) { $mesSeg = 1; $anoSeg++; }

            $limite = $this->diaUtilMes($anoSeg, $mesSeg, 5);

            if ($hoje > $limite) {
                $upd = $this->db->prepare("UPDATE mensalidades SET status='atrasado' WHERE id=:id");
                $upd->execute(['id' => $p['id']]);
                $atualizados++;
            }
        }
        return $atualizados;
    }

    // Dashboard: inadimplentes
    public function contarInadimplentes(?int $academiaId = null): int {
        $sql = "SELECT COUNT(DISTINCT m.aluno_id) FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                WHERE m.status IN ('pendente','atrasado') AND al.status='ativo'";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // Lista inadimplentes agrupados por responsável (para cobrança)
    public function listarInadimplentesParaCobranca(?int $academiaId = null): array {
        $sql = "SELECT
                    al.id AS aluno_id,
                    al.nome_completo,
                    al.faixa,
                    al.serie_nivel,
                    ac.nome AS academia_nome,
                    r.id   AS responsavel_id,
                    r.nome AS responsavel_nome,
                    r.telefone,
                    r.email,
                    COUNT(m.id)        AS total_pendencias,
                    SUM(m.valor)       AS total_valor,
                    MIN(m.data_vencimento) AS vencimento_mais_antigo,
                    GROUP_CONCAT(
                        CONCAT(m.mes_referencia, '|', m.status, '|', m.valor)
                        ORDER BY m.mes_referencia ASC
                        SEPARATOR ';'
                    ) AS pendencias_raw
                FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                JOIN academias ac ON ac.id = al.academia_id
                LEFT JOIN responsaveis r ON r.id = al.responsavel_id
                WHERE m.status IN ('pendente','atrasado')
                  AND al.status = 'ativo'";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " GROUP BY al.id, r.id
                  ORDER BY r.nome ASC, al.nome_completo ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Agrupa por responsável
        $porResponsavel = [];
        foreach ($rows as $row) {
            $rId = $row['responsavel_id'] ?? 'sem_responsavel';
            if (!isset($porResponsavel[$rId])) {
                $porResponsavel[$rId] = [
                    'responsavel_id'   => $row['responsavel_id'],
                    'responsavel_nome' => $row['responsavel_nome'] ?? 'Sem responsavel',
                    'telefone'         => $row['telefone'] ?? '',
                    'email'            => $row['email'] ?? '',
                    'alunos'           => [],
                    'total_valor'      => 0,
                    'total_pendencias' => 0,
                ];
            }
            // Parseia pendências individuais
            $pendencias = [];
            if ($row['pendencias_raw']) {
                foreach (explode(';', $row['pendencias_raw']) as $p) {
                    $parts = explode('|', $p);
                    if (count($parts) === 3) {
                        $pendencias[] = [
                            'mes'    => $parts[0],
                            'status' => $parts[1],
                            'valor'  => (float)$parts[2],
                        ];
                    }
                }
            }
            $porResponsavel[$rId]['alunos'][] = [
                'id'                   => $row['aluno_id'],
                'nome_completo'        => $row['nome_completo'],
                'faixa'                => $row['faixa'],
                'serie_nivel'          => $row['serie_nivel'],
                'academia_nome'        => $row['academia_nome'],
                'total_pendencias'     => (int)$row['total_pendencias'],
                'total_valor'          => (float)$row['total_valor'],
                'vencimento_mais_antigo' => $row['vencimento_mais_antigo'],
                'pendencias'           => $pendencias,
            ];
            $porResponsavel[$rId]['total_valor']      += (float)$row['total_valor'];
            $porResponsavel[$rId]['total_pendencias'] += (int)$row['total_pendencias'];
        }

        // Ordena: maior valor em aberto primeiro
        usort($porResponsavel, function($a, $b) {
            return $b['total_valor'] <=> $a['total_valor'];
        });

        return array_values($porResponsavel);
    }

    // Lançamentos atrasados
    public function proximosVencimentos(?int $academiaId = null): array {
        $this->atualizarAtrasados();

        $sql = "SELECT m.*, al.nome_completo
                FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                WHERE m.status = 'atrasado'";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " ORDER BY m.data_vencimento ASC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lançamentos futuros pendentes
    public function lancamentosFuturos(?int $academiaId = null): array {
        $sql = "SELECT m.*, al.nome_completo
                FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                WHERE m.status = 'pendente'";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $sql .= " ORDER BY m.data_vencimento ASC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Total a receber no mês subsequente
    public function totalMesSubsequente(?int $academiaId = null): float {
        $sql = "SELECT COALESCE(SUM(m.valor),0) FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                WHERE m.status = 'pendente'
                  AND m.mes_referencia = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')";
        $params = [];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float)$stmt->fetchColumn();
    }

    // Total recebido no mês
    public function totalRecebidoMes(string $mes, ?int $academiaId = null): float {
        $sql = "SELECT COALESCE(SUM(m.valor),0) FROM mensalidades m
                JOIN alunos al ON al.id = m.aluno_id
                WHERE m.status='pago' AND m.mes_referencia=:mes";
        $params = ['mes' => $mes];
        if ($academiaId) { $sql .= " AND al.academia_id=:a"; $params['a'] = $academiaId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float)$stmt->fetchColumn();
    }

    // Gerar mensalidades em lote para todos os alunos ativos de uma academia num mês
    public function gerarEmLote(int $academiaId, string $mes, float $valor, string $vencimento): int {
        $stmt = $this->db->prepare("SELECT id, bolsa_percentual FROM alunos WHERE academia_id=:a AND status='ativo'");
        $stmt->execute(['a' => $academiaId]);
        $alunos = $stmt->fetchAll();

        $criados = 0;
        foreach ($alunos as $aluno) {
            // Pula se já existe
            $existe = $this->buscarPorAlunoMes((int)$aluno['id'], $mes);
            if ($existe) continue;

            $bolsa = (int)$aluno['bolsa_percentual'];

            if ($bolsa === 100) {
                // Bolsa integral: marca como integral sem cobrança
                $this->criar([
                    'aluno_id'        => $aluno['id'],
                    'mes_referencia'  => $mes,
                    'valor'           => 0,
                    'status'          => 'integral',
                    'data_vencimento' => $vencimento,
                    'observacoes'     => 'Bolsa integral 100%',
                ]);
            } else {
                // Aplica desconto se houver bolsa
                $valorFinal = $valor;
                $obs = null;
                if ($bolsa > 0) {
                    $desconto = $valor * ($bolsa / 100);
                    $valorFinal = $valor - $desconto;
                    $obs = "Bolsa {$bolsa}%: desconto de R$ " . number_format($desconto, 2, ',', '.') . " (valor original R$ " . number_format($valor, 2, ',', '.') . ")";
                }
                $this->criar([
                    'aluno_id'        => $aluno['id'],
                    'mes_referencia'  => $mes,
                    'valor'           => $valorFinal,
                    'status'          => 'pendente',
                    'data_vencimento' => $vencimento,
                    'observacoes'     => $obs,
                ]);
            }
            $criados++;
        }
        return $criados;
    }
}

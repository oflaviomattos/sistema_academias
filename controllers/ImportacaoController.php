<?php
// ============================================================
// CONTROLLER: Importação CSV
// Baseado na estrutura real da planilha Google Sheets
// ============================================================
requireAdmin();

$academiaModel    = new AcademiaModel();
$responsavelModel = new ResponsavelModel();
$alunoModel       = new AlunoModel();
$mensalidadeModel = new MensalidadeModel();

switch ($page) {

    // ---- TELA INICIAL DE IMPORTAÇÃO ----
    case 'importacao':
        $academias = $academiaModel->listarAtivas();
        require_once __DIR__ . '/../views/importacao/index.php';
        break;

    // ---- PROCESSAR UPLOAD ----
    case 'importacao.upload':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('importacao'); }

        $tipo       = $_POST['tipo'] ?? '';         // alunos | mensalidades
        $academiaId = (int)($_POST['academia_id'] ?? 0);
        $arquivo    = $_FILES['csv'] ?? null;

        if (!$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK) {
            flashSet('danger', 'Erro no upload do arquivo.');
            redirect('importacao');
        }
        if (!$academiaId) {
            flashSet('danger', 'Selecione uma academia.');
            redirect('importacao');
        }

        // Valida extensão
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            flashSet('danger', 'Apenas arquivos .csv são aceitos.');
            redirect('importacao');
        }

        // Lê o arquivo
        $handle = fopen($arquivo['tmp_name'], 'r');
        if (!$handle) {
            flashSet('danger', 'Não foi possível ler o arquivo.');
            redirect('importacao');
        }

        // Detecta separador (vírgula ou ponto-e-vírgula)
        $primLinha = fgets($handle);
        rewind($handle);
        $separador = substr_count($primLinha, ';') > substr_count($primLinha, ',') ? ';' : ',';

        // Lê cabeçalho
        $cabecalho = fgetcsv($handle, 0, $separador);
        if (!$cabecalho) {
            fclose($handle);
            flashSet('danger', 'Arquivo CSV vazio ou inválido.');
            redirect('importacao');
        }

        // Normaliza cabeçalho: minúsculas, sem espaço
        $cabecalho = array_map(fn($c) => mb_strtolower(trim($c)), $cabecalho);

        $resultado = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'linhas' => 0];

        if ($tipo === 'alunos') {
            $resultado = importarAlunos($handle, $separador, $cabecalho, $academiaId,
                                        $alunoModel, $responsavelModel);
        } elseif ($tipo === 'mensalidades') {
            $resultado = importarMensalidades($handle, $separador, $cabecalho, $academiaId,
                                              $mensalidadeModel, $alunoModel);
        }

        fclose($handle);

        $_SESSION['import_resultado'] = $resultado;
        $_SESSION['import_tipo']      = $tipo;
        redirect('importacao');
        break;
}

// ============================================================
// FUNÇÃO: Importar Alunos
// Mapeamento de colunas da planilha:
//   manha / t → turno
//   contrato  → contrato_ok (ok = 1)
//   aluno     → nome_completo
//   #         → serie_nivel
//   faixa     → faixa
//   tamanho   → tamanho
//   responsavel → nome do responsável
//   contato   → telefone do responsável
// ============================================================
function importarAlunos($handle, string $sep, array $cab, int $academiaId,
                         AlunoModel $alunoModel, ResponsavelModel $responsavelModel): array {

    $res = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'linhas' => 0];

    // Mapeia índices das colunas aceitas (nomes alternativos incluídos)
    $mapa = [
        'turno'        => encontrarColuna($cab, ['manha / t','manha/t','turno','t']),
        'contrato'     => encontrarColuna($cab, ['contrato']),
        'nome'         => encontrarColuna($cab, ['aluno','nome','nome_completo','nome completo']),
        'serie'        => encontrarColuna($cab, ['#','serie','série','serie_nivel','nivel']),
        'faixa'        => encontrarColuna($cab, ['faixa']),
        'tamanho'      => encontrarColuna($cab, ['tamanho','tam']),
        'responsavel'  => encontrarColuna($cab, ['responsavel','responsável']),
        'contato'      => encontrarColuna($cab, ['contato','telefone','fone','cel','celular']),
        'nascimento'   => encontrarColuna($cab, ['data_nascimento','nascimento','data nasc','nasc']),
        'entrada'      => encontrarColuna($cab, ['data_entrada','entrada','data entrada']),
        'status'       => encontrarColuna($cab, ['status']),
    ];

    if ($mapa['nome'] === null) {
        $res['erros'][] = 'Coluna "aluno" ou "nome" não encontrada no CSV.';
        return $res;
    }

    $linha = 2; // já leu cabeçalho
    while (($row = fgetcsv($handle, 0, $sep)) !== false) {
        $res['linhas']++;

        // Normaliza linha
        $row = array_map('trim', $row);

        $nome = $row[$mapa['nome']] ?? '';
        if (!$nome || mb_strlen($nome) < 2) {
            $res['erros'][] = "Linha $linha: nome vazio, pulado.";
            $linha++; continue;
        }

        // Responsável: cria ou reutiliza
        $responsavelId = null;
        $nomeResp      = $mapa['responsavel'] !== null ? ($row[$mapa['responsavel']] ?? '') : '';
        $telefone      = $mapa['contato']     !== null ? ($row[$mapa['contato']] ?? '') : '';

        if ($nomeResp) {
            $db    = getDB();
            $stmt  = $db->prepare("SELECT id FROM responsaveis WHERE nome=:n LIMIT 1");
            $stmt->execute(['n' => $nomeResp]);
            $resp  = $stmt->fetchColumn();
            if ($resp) {
                $responsavelId = (int)$resp;
            } else {
                $responsavelId = $responsavelModel->criar([
                    'nome'     => $nomeResp,
                    'telefone' => $telefone ?: '—',
                    'email'    => null,
                ]);
            }
        }

        // Turno
        $turnoRaw = $mapa['turno'] !== null ? strtoupper($row[$mapa['turno']] ?? 'M') : 'M';
        if (strpos($turnoRaw, 'MT') !== false || strpos($turnoRaw, 'MN') !== false) {
            $turno = 'MT';
        } elseif (strpos($turnoRaw, 'T') !== false) {
            $turno = 'T';
        } elseif (strpos($turnoRaw, 'N') !== false) {
            $turno = 'N';
        } else {
            $turno = 'M';
        }

        // Faixa: normaliza (aceita "branca/cinza" → "branca")
        $faixaRaw = $mapa['faixa'] !== null ? strtolower($row[$mapa['faixa']] ?? 'branca') : 'branca';
        $faixa    = $faixaRaw ?: 'branca';

        // Série
        $serie = $mapa['serie'] !== null ? (int)($row[$mapa['serie']] ?? 0) : null;
        $serie = $serie > 0 ? $serie : null;

        // Contrato
        $contratoRaw = $mapa['contrato'] !== null ? strtolower($row[$mapa['contrato']] ?? '') : '';
        $contrato    = in_array($contratoRaw, ['ok','sim','s','1','yes','x']) ? 1 : 0;

        // Status
        $statusRaw = $mapa['status'] !== null ? strtolower($row[$mapa['status']] ?? 'ativo') : 'ativo';
        $status    = in_array($statusRaw, ['inativo','0','no','não']) ? 'inativo' : 'ativo';

        // Datas
        $nascimento = parsearData($mapa['nascimento'] !== null ? ($row[$mapa['nascimento']] ?? '') : '');
        $entrada    = parsearData($mapa['entrada']    !== null ? ($row[$mapa['entrada']] ?? '') : '') ?? date('Y-m-d');

        // Verifica se já existe pelo nome + academia
        $db   = getDB();
        $stmt = $db->prepare("SELECT id FROM alunos WHERE nome_completo=:n AND academia_id=:a LIMIT 1");
        $stmt->execute(['n' => $nome, 'a' => $academiaId]);
        $existeId = $stmt->fetchColumn();

        $dados = [
            'nome_completo'   => $nome,
            'data_nascimento' => $nascimento,
            'turno'           => $turno,
            'contrato_ok'     => $contrato,
            'faixa'           => $faixa,
            'serie_nivel'     => $serie,
            'tamanho'         => $mapa['tamanho'] !== null ? ($row[$mapa['tamanho']] ?? '') : '',
            'status'          => $status,
            'data_entrada'    => $entrada,
            'academia_id'     => $academiaId,
            'responsavel_id'  => $responsavelId,
            'observacoes'     => null,
        ];

        try {
            if ($existeId) {
                $alunoModel->atualizar((int)$existeId, $dados);
                $res['atualizados']++;
            } else {
                $alunoModel->criar($dados);
                $res['criados']++;
            }
        } catch (Exception $e) {
            $res['erros'][] = "Linha $linha: " . $e->getMessage();
        }

        $linha++;
    }

    return $res;
}

// ============================================================
// FUNÇÃO: Importar Mensalidades
// Detecta automaticamente colunas de meses (Jan, Fev, Mar...)
// Cada coluna de mês contém: "Pix DD/MM", "ATRASADO", "Pendente", etc.
// ============================================================
function importarMensalidades($handle, string $sep, array $cab, int $academiaId,
                               MensalidadeModel $mensalidadeModel, AlunoModel $alunoModel): array {

    $res = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'linhas' => 0];

    // Detecta o ano das mensalidades (via POST ou atual)
    $ano = (int)($_POST['ano'] ?? date('Y'));

    // Meses aceitos (PT e abreviações)
    $mesesMap = [
        'jan'=>'01','fev'=>'02','mar'=>'03','abr'=>'04','mai'=>'05','jun'=>'06',
        'jul'=>'07','ago'=>'08','set'=>'09','out'=>'10','nov'=>'11','dez'=>'12',
        'janeiro'=>'01','fevereiro'=>'02','março'=>'03','abril'=>'04','maio'=>'05',
        'junho'=>'06','julho'=>'07','agosto'=>'08','setembro'=>'09','outubro'=>'10',
        'novembro'=>'11','dezembro'=>'12',
    ];

    // Localiza coluna "aluno" e colunas de meses
    $colAluno   = encontrarColuna($cab, ['aluno','nome','nome_completo','nome completo']);
    $colMeses   = []; // ['01' => idx, '02' => idx, ...]

    foreach ($cab as $idx => $col) {
        $colLimpa = mb_strtolower(preg_replace('/[^a-záéíóúã]/u', '', $col));
        if (isset($mesesMap[$colLimpa])) {
            $colMeses[$mesesMap[$colLimpa]] = $idx;
        }
    }

    if ($colAluno === null) {
        $res['erros'][] = 'Coluna "aluno" não encontrada.';
        return $res;
    }
    if (empty($colMeses)) {
        $res['erros'][] = 'Nenhuma coluna de mês (Jan, Fev...) encontrada.';
        return $res;
    }

    // Valor padrão da mensalidade (via POST)
    $valorPadrao = (float)str_replace(',', '.', $_POST['valor_padrao'] ?? '0');

    $linha = 2;
    while (($row = fgetcsv($handle, 0, $sep)) !== false) {
        $res['linhas']++;
        $row  = array_map('trim', $row);
        $nome = $row[$colAluno] ?? '';

        if (!$nome || mb_strlen($nome) < 2) { $linha++; continue; }

        // Encontra o aluno no banco pelo nome + academia
        $db   = getDB();
        $stmt = $db->prepare(
            "SELECT id FROM alunos WHERE nome_completo=:n AND academia_id=:a LIMIT 1"
        );
        $stmt->execute(['n' => $nome, 'a' => $academiaId]);
        $alunoId = $stmt->fetchColumn();

        if (!$alunoId) {
            $res['erros'][] = "Linha $linha: aluno '$nome' não encontrado no banco. Importe os alunos primeiro.";
            $linha++; continue;
        }

        // Processa cada coluna de mês
        foreach ($colMeses as $mes => $colIdx) {
            $celula = strtolower(trim($row[$colIdx] ?? ''));
            if ($celula === '' || $celula === '-') continue; // célula vazia

            // Determina status e forma de pagamento
            $status          = 'pendente';
            $formaPagamento  = null;
            $dataPagamento   = null;

            if (strpos($celula, 'integral') !== false || $celula === 'integral') {
                $status = 'integral';
            } elseif (strpos($celula, 'atrasado') !== false) {
                $status = 'atrasado';
            } elseif (strpos($celula, 'quitado') !== false || strpos($celula, 'pago') !== false) {
                $status = 'pago';
                $formaPagamento = 'Dinheiro';
            } elseif (strpos($celula, 'pix') !== false) {
                $status = 'pago';
                $formaPagamento = 'Pix';
                if (preg_match('/(\d{1,2})\/(\d{1,2})/', $celula, $m)) {
                    $dia  = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    $mesP = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                    $dataPagamento = "$ano-$mesP-$dia";
                } else {
                    $dataPagamento = date('Y-m-d');
                }
            } elseif (strpos($celula, 'pendente') !== false) {
                $status = 'pendente';
            }

            $mesRef    = "$ano-$mes";
            $vencimento = "$ano-$mes-10"; // vencimento padrão dia 10

            // Verifica se já existe
            $existente = $mensalidadeModel->buscarPorAlunoMes((int)$alunoId, $mesRef);

            try {
                if ($existente) {
                    // Só atualiza se não estava pago
                    if ($existente['status'] !== 'pago') {
                        $mensalidadeModel->atualizar((int)$existente['id'], [
                            'valor'           => $valorPadrao ?: $existente['valor'],
                            'status'          => $status,
                            'forma_pagamento' => $formaPagamento,
                            'data_vencimento' => $existente['data_vencimento'],
                            'data_pagamento'  => $dataPagamento,
                            'observacoes'     => $celula,
                        ]);
                        $res['atualizados']++;
                    }
                } else {
                    $mensalidadeModel->criar([
                        'aluno_id'        => $alunoId,
                        'mes_referencia'  => $mesRef,
                        'valor'           => $valorPadrao,
                        'status'          => $status,
                        'forma_pagamento' => $formaPagamento,
                        'data_vencimento' => $vencimento,
                        'data_pagamento'  => $dataPagamento,
                        'observacoes'     => $celula,
                    ]);
                    $res['criados']++;
                }
            } catch (Exception $e) {
                $res['erros'][] = "Linha $linha / Mês $mes: " . $e->getMessage();
            }
        }

        $linha++;
    }

    return $res;
}

// ============================================================
// HELPERS
// ============================================================
function encontrarColuna(array $cabecalho, array $opcoes): ?int {
    foreach ($opcoes as $opcao) {
        $idx = array_search($opcao, $cabecalho);
        if ($idx !== false) return (int)$idx;
    }
    return null;
}

function parsearData(string $raw): ?string {
    if (!$raw || $raw === '-') return null;
    // Tenta formatos dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd
    foreach (['d/m/Y','d-m-Y','Y-m-d','d/m/y'] as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $raw);
        if ($dt) return $dt->format('Y-m-d');
    }
    return null;
}

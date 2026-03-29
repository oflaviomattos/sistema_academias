<?php $pageTitle = 'Dashboard'; require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- CARDS RESUMO -->
<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon">👥</div>
    <div class="label">Alunos Ativos</div>
    <div class="value"><?= $dados['total_ativos'] ?></div>
    <div class="sub">matriculados</div>
  </div>

  <!-- Card inadimplentes — clicável -->
  <a href="<?= APP_URL ?>/index.php?page=dashboard&cobranca=1<?= !empty($dados['inadimplentes']) ? '#cobranca' : '' ?>"
     style="text-decoration:none">
    <div class="stat-card red" style="cursor:pointer;transition:box-shadow .15s;border:1px solid #fca5a5"
         onmouseover="this.style.boxShadow='0 4px 16px rgba(220,38,38,.2)'"
         onmouseout="this.style.boxShadow=''">
      <div class="stat-icon">⚠️</div>
      <div class="label">Inadimplentes</div>
      <div class="value"><?= $dados['total_inadimplentes'] ?></div>
      <div class="sub" style="color:#ef4444">clique para ver e cobrar →</div>
    </div>
  </a>

  <div class="stat-card green">
    <div class="stat-icon">💰</div>
    <div class="label">Recebido em <?= date('m/Y') ?></div>
    <div class="value" style="font-size:20px"><?= formatMoeda($dados['total_recebido_mes']) ?></div>
    <div class="sub">mensalidades pagas</div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon">📅</div>
    <div class="label">Vencem em 7 dias</div>
    <div class="value"><?= count($dados['proximos_vencimentos']) ?></div>
    <div class="sub">mensalidades</div>
  </div>
</div>

<!-- ============================================================
     PAINEL DE COBRANÇA (aparece ao clicar no card vermelho)
============================================================ -->
<?php if (!empty($dados['inadimplentes'])): ?>
<div id="cobranca" class="card" style="margin-bottom:24px;border:2px solid #fca5a5">
  <div class="card-header" style="background:#fff5f5">
    <span>📞</span>
    <h2 style="color:#dc2626">Lista de Cobrança</h2>
    <span class="badge badge-danger" style="margin-left:8px"><?= count($dados['inadimplentes']) ?> responsaveis</span>
    <div class="ms-auto d-flex gap-8">
      <!-- Botão copiar todos os WhatsApp -->
      <button onclick="copiarTodosContatos()" class="btn btn-outline btn-sm">
        📋 Copiar todos os contatos
      </button>
      <a href="<?= APP_URL ?>/index.php?page=dashboard" class="btn btn-outline btn-sm">✕ Fechar</a>
    </div>
  </div>

  <!-- Resumo total -->
  <?php
    $totalGeralValor = 0;
    $totalGeralAlunos = 0;
    foreach ($dados['inadimplentes'] as $resp) {
        $totalGeralValor  += $resp['total_valor'];
        $totalGeralAlunos += count($resp['alunos']);
    }
  ?>
  <div style="padding:12px 20px;background:#fef2f2;border-bottom:1px solid #fecaca;display:flex;gap:24px;font-size:13px">
    <span>👨‍👩‍👧 <strong><?= $totalGeralAlunos ?></strong> alunos inadimplentes</span>
    <span>💰 Total em aberto: <strong style="color:#dc2626"><?= formatMoeda($totalGeralValor) ?></strong></span>
    <span>📅 Gerado em: <?= date('d/m/Y H:i') ?></span>
  </div>

  <div id="lista-cobranca">
  <?php foreach ($dados['inadimplentes'] as $idx => $resp):
    $tel     = preg_replace('/\D/', '', $resp['telefone']);
    $temTel  = strlen($tel) >= 10;
  ?>
  <div class="responsavel-cobranca" style="border-bottom:1px solid #fee2e2;padding:16px 20px"
       data-nome="<?= h($resp['responsavel_nome']) ?>"
       data-tel="<?= h($resp['telefone']) ?>">

    <div style="display:flex;align-items:flex-start;gap:14px">

      <!-- Avatar -->
      <div style="width:42px;height:42px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
        👤
      </div>

      <!-- Info responsável -->
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <span style="font-weight:700;font-size:15px"><?= h($resp['responsavel_nome']) ?></span>
          <span class="badge badge-danger"><?= formatMoeda($resp['total_valor']) ?> em aberto</span>
          <span class="badge badge-secondary"><?= $resp['total_pendencias'] ?> mês(es)</span>
        </div>

        <!-- Contato -->
        <div style="display:flex;gap:12px;margin-top:6px;flex-wrap:wrap;align-items:center">
          <?php if ($temTel): ?>
          <a href="https://wa.me/55<?= $tel ?>"
             target="_blank"
             style="display:inline-flex;align-items:center;gap:6px;background:#25D366;color:#fff;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.122 1.528 5.857L0 24l6.337-1.509A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.65-.498-5.178-1.367l-.371-.22-3.761.896.958-3.663-.244-.389A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            <?= h($resp['telefone']) ?>
          </a>
          <button onclick="gerarMensagem(<?= $idx ?>)"
                  class="btn btn-outline btn-sm"
                  title="Copiar mensagem de cobrança">
            📝 Copiar mensagem
          </button>
          <?php else: ?>
          <span style="font-size:13px;color:#94a3b8">📵 Sem telefone cadastrado</span>
          <?php endif; ?>

          <?php if ($resp['email']): ?>
          <a href="mailto:<?= h($resp['email']) ?>"
             style="font-size:13px;color:#1d4ed8">✉️ <?= h($resp['email']) ?></a>
          <?php endif; ?>
        </div>

        <!-- Alunos deste responsável -->
        <div style="margin-top:10px;display:flex;flex-direction:column;gap:6px">
          <?php foreach ($resp['alunos'] as $aluno): ?>
          <div style="background:#fff;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <span class="faixa-badge faixa-<?= strtolower(explode('/',$aluno['faixa'])[0]) ?>">
              <?= h($aluno['faixa']) ?>
            </span>
            <a href="<?= APP_URL ?>/index.php?page=alunos.ver&id=<?= $aluno['id'] ?>"
               style="font-weight:600;font-size:13.5px;color:#1e293b;text-decoration:none;flex:1">
              <?= h($aluno['nome_completo']) ?>
            </a>
            <span style="font-size:12px;color:#64748b"><?= h($aluno['academia_nome']) ?></span>
            <span class="badge badge-danger" style="flex-shrink:0"><?= formatMoeda($aluno['total_valor']) ?></span>

            <!-- Meses pendentes -->
            <div style="width:100%;display:flex;gap:4px;flex-wrap:wrap;margin-top:2px">
              <?php foreach ($aluno['pendencias'] as $p): ?>
              <?php
                $bclass = $p['status'] === 'atrasado' ? 'badge-danger' : 'badge-warning';
                $icon   = $p['status'] === 'atrasado' ? '🔴' : '⏳';
              ?>
              <span class="badge <?= $bclass ?>" style="font-size:11px">
                <?= $icon ?> <?= mesReferencia($p['mes']) ?> — <?= formatMoeda($p['valor']) ?>
              </span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Ação rápida: registrar pagamento -->
      <div style="flex-shrink:0;display:flex;flex-direction:column;gap:6px">
        <a href="<?= APP_URL ?>/index.php?page=financeiro&busca=<?= urlencode($resp['alunos'][0]['nome_completo'] ?? '') ?>"
           class="btn btn-success btn-sm">
          ✅ Registrar pagamento
        </a>
      </div>

    </div>
  </div>

  <!-- Dados hidden para geração de mensagem -->
  <script>
  window.inadimplentes = window.inadimplentes || [];
  window.inadimplentes[<?= $idx ?>] = {
      nome: <?= json_encode($resp['responsavel_nome']) ?>,
      alunos: <?= json_encode(array_map(function($a) {
          return [
              'nome'     => $a['nome_completo'],
              'valor'    => (float)$a['total_valor'],
              'academia' => $a['academia_nome'],
              'meses'    => array_map(function($p) { return $p['mes']; }, $a['pendencias']),
              'pendencias' => array_map(function($p) {
                  return [
                      'mes'    => $p['mes'],
                      'status' => $p['status'],
                      'valor'  => (float)$p['valor'],
                  ];
              }, $a['pendencias']),
          ];
      }, $resp['alunos'])) ?>,
      total: <?= (float)$resp['total_valor'] ?>,
      tel: <?= json_encode($tel) ?>
  };
  </script>

  <?php endforeach; ?>
  </div>
</div>
<?php elseif (isset($_GET['cobranca']) && $_GET['cobranca'] === '1'): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-body" style="text-align:center;padding:40px">
    <div style="font-size:48px;margin-bottom:12px">🎉</div>
    <h3 style="color:#16a34a;font-size:18px">Nenhum inadimplente!</h3>
    <p style="color:#64748b;margin-top:8px">Todos os alunos estão com as mensalidades em dia.</p>
    <a href="<?= APP_URL ?>/index.php?page=dashboard" class="btn btn-outline" style="margin-top:16px">← Voltar</a>
  </div>
</div>
<?php endif; ?>

<!-- GRADE PRINCIPAL -->
<div class="dash-grid">

  <div class="card">
    <div class="card-header">
      <span>⏳</span>
      <h2>Proximos Vencimentos</h2>
      <a href="<?= BASE_URL ?>/?page=financeiro&status=pendente" class="btn btn-outline btn-sm">Ver todos</a>
    </div>
    <div class="card-body" style="padding:0">
      <?php if (empty($dados['proximos_vencimentos'])): ?>
        <div class="empty-state" style="padding:28px">
          <div>✅</div><h3>Tudo em dia!</h3>
          <p>Nenhum vencimento nos proximos 7 dias.</p>
        </div>
      <?php else: ?>
        <table>
          <thead><tr><th>Aluno</th><th>Vencimento</th><th>Valor</th></tr></thead>
          <tbody>
          <?php foreach ($dados['proximos_vencimentos'] as $m): ?>
            <tr>
              <td><?= h($m['nome_completo']) ?></td>
              <td><?= formatData($m['data_vencimento']) ?></td>
              <td><?= formatMoeda($m['valor']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span>🎽</span>
      <h2>Proximos Exames de Faixa</h2>
      <a href="<?= BASE_URL ?>/?page=exames" class="btn btn-outline btn-sm">Ver todos</a>
    </div>
    <div class="card-body" style="padding:0">
      <?php if (empty($dados['proximos_exames'])): ?>
        <div class="empty-state" style="padding:28px"><div>📭</div><h3>Nenhum exame agendado</h3></div>
      <?php else: ?>
        <table>
          <thead><tr><th>Aluno</th><th>Faixa</th><th>Nova</th><th>Data</th></tr></thead>
          <tbody>
          <?php foreach ($dados['proximos_exames'] as $e): ?>
            <tr>
              <td><?= h($e['nome_completo']) ?></td>
              <td><span class="faixa-badge faixa-<?= h($e['faixa_atual']) ?>"><?= h($e['faixa_atual']) ?></span></td>
              <td><span class="faixa-badge faixa-<?= h($e['nova_faixa']) ?>"><?= h($e['nova_faixa']) ?></span></td>
              <td><?= formatData($e['data_exame']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span>🏆</span>
      <h2>Proximos Campeonatos</h2>
      <a href="<?= BASE_URL ?>/?page=campeonatos" class="btn btn-outline btn-sm">Ver todos</a>
    </div>
    <div class="card-body" style="padding:0">
      <?php if (empty($dados['proximos_campeonatos'])): ?>
        <div class="empty-state" style="padding:28px"><div>📭</div><h3>Nenhum campeonato agendado</h3></div>
      <?php else: ?>
        <table>
          <thead><tr><th>Nome</th><th>Data</th><th>Local</th></tr></thead>
          <tbody>
          <?php foreach ($dados['proximos_campeonatos'] as $c): ?>
            <tr>
              <td class="fw-600"><?= h($c['nome']) ?></td>
              <td><?= formatData($c['data']) ?></td>
              <td><?= h($c['local'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span>⚡</span><h2>Acoes Rapidas</h2></div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
      <a href="<?= BASE_URL ?>/?page=alunos.criar" class="btn btn-primary">+ Novo Aluno</a>
      <a href="<?= BASE_URL ?>/?page=financeiro.lancar" class="btn btn-success">+ Lancar Mensalidade</a>
      <a href="<?= BASE_URL ?>/?page=exames.criar" class="btn btn-secondary">+ Agendar Exame</a>
      <a href="<?= BASE_URL ?>/?page=campeonatos.criar" class="btn btn-secondary">+ Novo Campeonato</a>
      <?php if (isAdmin()): ?>
      <hr style="border:none;border-top:1px solid #e2e8f0">
      <a href="<?= BASE_URL ?>/?page=financeiro.gerar" class="btn btn-warning">⚙ Gerar Mensalidades em Lote</a>
      <a href="<?= BASE_URL ?>/?page=importacao" class="btn btn-outline">📥 Importar CSV</a>
      <?php endif; ?>
    </div>
  </div>

</div>

<script>
// Template da mensagem vindo do banco
var msgTemplate = <?php
    try {
        $cfgModel = new ConfiguracaoModel();
        $tpl = $cfgModel->get('mensagem_cobranca', '');
        $pix = $cfgModel->get('chave_pix', '');
        $prof = $cfgModel->get('nome_professor', '');
    } catch(Exception $e) { $tpl=''; $pix=''; $prof=''; }
    echo json_encode($tpl);
?>;
var pixConfig  = <?php echo json_encode($pix ?? ''); ?>;
var profConfig = <?php echo json_encode($prof ?? ''); ?>;

// ── Gerar mensagem de cobrança personalizada ──────────────────
function gerarMensagem(idx) {
    var d = window.inadimplentes[idx];
    if (!d) return;

    var tpl = msgTemplate || 'Ola {responsavel}!\nMensalidade de {aluno} em aberto: {valor_total}.\nPIX: {pix}\n{professor}';

    var nomesMeses = ['Janeiro','Fevereiro','Marco','Abril','Maio','Junho',
                      'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

    function formatarMes(m) {
        var p = m.split('-');
        return nomesMeses[parseInt(p[1])-1] + '/' + p[0];
    }

    // Se há só 1 aluno: usa dados desse aluno direto
    // Se há 2+: gera uma linha por aluno com nome, meses e valor
    var nomesAlunos, linhaAlunos, mesRef, valorRef, acadRef;

    if (d.alunos.length === 1) {
        var a = d.alunos[0];
        nomesAlunos = a.nome;
        acadRef     = a.academia || '';
        mesRef      = a.meses.map(formatarMes).join(', ');
        valorRef    = 'R$ ' + a.valor.toFixed(2).replace('.',',');
        linhaAlunos = nomesAlunos;
    } else {
        // Múltiplos alunos: lista cada um com seus meses e valor
        nomesAlunos = d.alunos.map(function(a){ return a.nome; }).join(' e ');
        acadRef     = d.alunos[0].academia || '';

        // Coleta todos os meses únicos para o campo {mes}
        var todosMeses = [];
        d.alunos.forEach(function(a) {
            a.meses.forEach(function(m) {
                var mf = formatarMes(m);
                if (todosMeses.indexOf(mf) === -1) todosMeses.push(mf);
            });
        });
        mesRef   = todosMeses.join(', ');
        valorRef = 'R$ ' + d.total.toFixed(2).replace('.',',');

        // Detalhe por aluno para {aluno} quando há múltiplos
        linhaAlunos = d.alunos.map(function(a) {
            var meses = a.meses.map(formatarMes).join(', ');
            return a.nome + ' (' + meses + ' — R$ ' + a.valor.toFixed(2).replace('.',',') + ')';
        }).join('\n');
    }

    var msg = tpl
        .replace(/{responsavel}/g, d.nome)
        .replace(/{aluno}/g,       linhaAlunos)
        .replace(/{academia}/g,    acadRef)
        .replace(/{mes}/g,         mesRef)
        .replace(/{vencimento}/g,  mesRef)
        .replace(/{valor}/g,       valorRef)
        .replace(/{valor_total}/g, 'R$ ' + d.total.toFixed(2).replace('.',','))
        .replace(/{pix}/g,         pixConfig)
        .replace(/{professor}/g,   profConfig);

    if (navigator.clipboard) {
        navigator.clipboard.writeText(msg).then(function() {
            mostrarAviso('Mensagem copiada! Cole no WhatsApp.');
        });
    } else {
        prompt('Copie a mensagem:', msg);
    }
}

// ── Copiar todos os contatos ──────────────────────────────────
function copiarTodosContatos() {
    var linhas = [];
    document.querySelectorAll('.responsavel-cobranca').forEach(function(el) {
        var nome = el.dataset.nome;
        var tel  = el.dataset.tel;
        if (nome) linhas.push(nome + (tel ? ' — ' + tel : ' — sem telefone'));
    });
    var texto = 'Lista de cobrança — ' + new Date().toLocaleDateString('pt-BR') + '\n\n'
              + linhas.join('\n');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(function() {
            mostrarAviso('Lista copiada com ' + linhas.length + ' contato(s)!');
        });
    } else {
        prompt('Copie a lista:', texto);
    }
}

// ── Toast de aviso ────────────────────────────────────────────
function mostrarAviso(msg) {
    var t = document.createElement('div');
    t.textContent = '✓ ' + msg;
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;'
                    + 'padding:12px 20px;border-radius:8px;font-size:14px;z-index:9999;'
                    + 'box-shadow:0 4px 16px rgba(0,0,0,.3);transition:opacity .4s';
    document.body.appendChild(t);
    setTimeout(function() { t.style.opacity = '0'; setTimeout(function() { t.remove(); }, 400); }, 3000);
}

// Scroll automático para o painel de cobrança ao carregar
<?php if (!empty($dados['inadimplentes'])): ?>
setTimeout(function() {
    var el = document.getElementById('cobranca');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}, 200);
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

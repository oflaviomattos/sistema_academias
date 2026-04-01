<?php $pageTitle = h($aluno['nome_completo']); require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">

  <?php if (!empty($pendenciasAtrasadas)): ?>
  <?php $tel = preg_replace('/\D/', '', $aluno['responsavel_telefone'] ?? ''); ?>
  <!-- COBRANÇA DESTACADA -->
  <div style="grid-column:1/-1;background:#fef2f2;border:2px solid #fca5a5;border-radius:10px;padding:16px 20px">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <div style="font-size:13px;color:#dc2626;font-weight:700;flex:1;min-width:200px">
        🔴 <?= count($pendenciasAtrasadas) ?> mensalidade(s) atrasada(s) — <?= formatMoeda($totalAtrasado) ?>
      </div>
      <div style="display:flex;gap:8px">
        <button onclick="copiarCobrancaIndividual()" class="btn btn-outline btn-sm">
          📝 Copiar mensagem
        </button>
        <?php if ($tel): ?>
        <a href="https://wa.me/55<?= $tel ?>?text=<?= urlencode('') ?>" target="_blank"
           onclick="this.href='https://wa.me/55<?= $tel ?>?text='+encodeURIComponent(gerarMsgCobranca())"
           style="display:inline-flex;align-items:center;justify-content:center;gap:6px;
                  background:#25D366;color:#fff;padding:6px 14px;border-radius:6px;
                  font-size:13px;font-weight:600;text-decoration:none">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.122 1.528 5.857L0 24l6.337-1.509A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.65-.498-5.178-1.367l-.371-.22-3.761.896.958-3.663-.244-.389A9.946 9.946 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
          </svg>
          WhatsApp
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- FICHA DO ALUNO -->
  <div class="card">
    <div class="card-header">
      <span>👤</span>
      <h2>Ficha do Aluno</h2>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

      <div style="text-align:center;padding:12px 0">
        <div style="font-size:48px">🥋</div>
        <div style="font-size:17px;font-weight:700;margin-top:8px"><?= h($aluno['nome_completo']) ?></div>
        <?php if ($aluno['status']==='ativo'): ?>
          <span class="badge badge-success">Ativo</span>
        <?php else: ?>
          <span class="badge badge-secondary">Inativo</span>
        <?php endif; ?>
      </div>

      <hr style="border:none;border-top:1px solid #f1f5f9">

      <div style="display:grid;gap:10px;font-size:13px">
        <div><span class="text-muted">Academia:</span><br>
          <strong><?= h($aluno['academia_nome'] ?? '-') ?></strong></div>
        <div><span class="text-muted">Turno:</span>
          <span class="turno-<?= strtolower($aluno['turno']) ?>" style="margin-left:6px"><?= TURNOS[$aluno['turno']] ?? $aluno['turno'] ?></span>
        </div>
        <div><span class="text-muted">Série:</span>
          <strong>
            <?php
            $sn = $aluno['serie_nivel'] ?? '';
            if (strpos($sn, 'Infantil ') === 0) {
                echo 'Infantil ' . substr($sn, 9) . ' ano' . (substr($sn, 9) > 1 ? 's' : '');
            } elseif (strpos($sn, 'Fund I ') === 0) {
                echo 'Fundamental I - ' . substr($sn, 7) . 'º ano';
            } elseif (strpos($sn, 'Fund II ') === 0) {
                echo 'Fundamental II - ' . substr($sn, 8) . 'º ano';
            } else {
                echo $sn ? h($sn) : '-';
            }
            ?>
          </strong></div>
        <div><span class="text-muted">Faixa:</span>
          <span class="faixa-badge faixa-<?= strtolower(explode('/',$aluno['faixa'])[0]) ?>" style="margin-left:6px"><?= h($aluno['faixa']) ?></span>
        </div>
        <div><span class="text-muted">Tamanho:</span> <strong><?= h($aluno['tamanho'] ?? '-') ?></strong></div>
        <div><span class="text-muted">Peso:</span> <strong><?= $aluno['peso'] ? number_format((float)$aluno['peso'], 2, ',', '.') . ' kg' : '-' ?></strong></div>
        <div><span class="text-muted">Bolsa:</span>
          <?php if (!empty($aluno['bolsa_percentual'])): ?>
            <span class="badge badge-info"><?= $aluno['bolsa_percentual'] ?>%<?= $aluno['bolsa_percentual']==100?' (integral)':'' ?></span>
          <?php else: ?>
            <strong>-</strong>
          <?php endif; ?>
        </div>
        <div><span class="text-muted">Nascimento:</span> <strong><?= formatData($aluno['data_nascimento']) ?></strong></div>
        <div><span class="text-muted">Entrada:</span> <strong><?= formatData($aluno['data_entrada']) ?></strong></div>
        <div><span class="text-muted">Contrato:</span>
          <?php if ($aluno['contrato_ok']): ?>
            <span class="contrato-ok">✓ OK</span>
          <?php else: ?>
            <span class="contrato-no">Pendente</span>
          <?php endif; ?>
        </div>
      </div>

      <hr style="border:none;border-top:1px solid #f1f5f9">

      <div style="font-size:13px">
        <div class="text-muted" style="margin-bottom:4px">Responsável</div>
        <strong><?= h($aluno['responsavel_nome'] ?? 'Não informado') ?></strong>
        <?php if ($aluno['responsavel_telefone']): ?>
          <br><a href="https://wa.me/55<?= preg_replace('/\D/','',$aluno['responsavel_telefone']) ?>"
                 target="_blank" style="color:#25D366;font-size:13px">
            📱 <?= h($aluno['responsavel_telefone']) ?>
          </a>
        <?php endif; ?>
      </div>

      <?php if ($aluno['observacoes']): ?>
      <hr style="border:none;border-top:1px solid #f1f5f9">
      <div style="font-size:13px">
        <div class="text-muted" style="margin-bottom:4px">Observações</div>
        <?= h($aluno['observacoes']) ?>
      </div>
      <?php endif; ?>

      <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
        <a href="<?= BASE_URL ?>/?page=alunos.editar&id=<?= $aluno['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
        <a href="<?= BASE_URL ?>/?page=financeiro.lancar&aluno_id=<?= $aluno['id'] ?>" class="btn btn-success btn-sm">+ Lançar Mensalidade</a>
        <a href="<?= BASE_URL ?>/?page=exames.criar&aluno_id=<?= $aluno['id'] ?>" class="btn btn-outline btn-sm">🎽 Agendar Exame</a>
      </div>
    </div>
  </div>

  <!-- MENSALIDADES DO ALUNO -->
  <div class="card">
    <div class="card-header">
      <span>💰</span>
      <h2>Histórico de Mensalidades</h2>
      <a href="<?= BASE_URL ?>/?page=financeiro.lancar&aluno_id=<?= $aluno['id'] ?>"
         class="btn btn-success btn-sm ms-auto">+ Lançar</a>
    </div>
    <?php if (empty($mensalidades)): ?>
      <div class="empty-state"><div class="icon">💳</div><h3>Nenhuma mensalidade registrada</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Mês</th><th>Valor</th><th>Vencimento</th><th>Status</th><th>Pagamento</th><th>Forma</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($mensalidades as $m): ?>
          <tr>
            <td class="fw-600"><?= mesReferencia($m['mes_referencia']) ?></td>
            <td><?= formatMoeda($m['valor']) ?></td>
            <td><?= formatData($m['data_vencimento']) ?></td>
            <td>
              <?php
              $sc = ['pago'=>'success','pendente'=>'warning','atrasado'=>'danger','integral'=>'info'];
              $sc = $sc[$m['status']] ?? 'secondary';
              ?>
              <span class="badge badge-<?= $sc ?>"><?= h(ucfirst($m['status'])) ?></span>
            </td>
            <td><?= formatData($m['data_pagamento']) ?></td>
            <td><?= h($m['forma_pagamento'] ?? '-') ?></td>
            <td>
              <?php if ($m['status'] !== 'pago'): ?>
                <?php if ($m['status'] !== 'integral'): ?>
                <a href="<?= BASE_URL ?>/?page=financeiro.pagar&id=<?= $m['id'] ?>"
                   class="btn btn-success btn-xs" title="Acusar Pagamento">✓</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/?page=financeiro.cancelar&id=<?= $m['id'] ?>"
                   class="btn btn-outline btn-xs"
                   data-confirm="Cancelar e excluir esta cobrança?" title="Cancelar lancamento">🚫</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php if (!empty($pendenciasAtrasadas) && !empty($aluno['responsavel_telefone'])): ?>
<script>
var pendenciasCobranca = <?= json_encode(array_map(function($m) {
    return [
        'mes'   => $m['mes_referencia'],
        'valor' => (float)$m['valor'],
        'status'=> $m['status'],
    ];
}, array_values($pendenciasAtrasadas))) ?>;
var alunoCobranca = {
    nome: <?= json_encode($aluno['nome_completo'], JSON_UNESCAPED_UNICODE) ?>,
    responsavel: <?= json_encode($aluno['responsavel_nome'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
    academia: <?= json_encode($aluno['academia_nome'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
    total: <?= (float)$totalAtrasado ?>
};
<?php
    try {
        $cfgModel = new ConfiguracaoModel();
        $tpl = $cfgModel->get('mensagem_cobranca', '');
        $pix = $cfgModel->get('chave_pix', '');
        $prof = $cfgModel->get('nome_professor', '');
    } catch(Exception $e) { $tpl=''; $pix=''; $prof=''; }
?>
var msgTemplateCob = <?= json_encode($tpl) ?>;
var pixCob  = <?= json_encode($pix) ?>;
var profCob = <?= json_encode($prof) ?>;

var nomesMeses = ['Janeiro','Fevereiro','Marco','Abril','Maio','Junho',
                  'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

function formatarMesRef(m) {
    var p = m.split('-');
    return nomesMeses[parseInt(p[1])-1] + '/' + p[0];
}

function gerarMsgCobranca() {
    var tpl = msgTemplateCob || 'Ola {responsavel}!\nMensalidade de {aluno} em aberto: {valor_total}.\nPIX: {pix}\n{professor}';

    var mesesStr = pendenciasCobranca.map(function(p) { return formatarMesRef(p.mes); }).join(', ');
    var detalhe = pendenciasCobranca.map(function(p) {
        return formatarMesRef(p.mes) + ' - R$ ' + p.valor.toFixed(2).replace('.',',');
    }).join('\n');

    var msg = tpl
        .replace(/{responsavel}/g,  alunoCobranca.responsavel)
        .replace(/{aluno}/g,        alunoCobranca.nome)
        .replace(/{academia}/g,     alunoCobranca.academia)
        .replace(/{mes}/g,          mesesStr)
        .replace(/{vencimento}/g,   mesesStr)
        .replace(/{valor}/g,        'R$ ' + alunoCobranca.total.toFixed(2).replace('.',','))
        .replace(/{valor_total}/g,  'R$ ' + alunoCobranca.total.toFixed(2).replace('.',','))
        .replace(/{pix}/g,          pixCob)
        .replace(/{professor}/g,    profCob);
    return msg;
}

function copiarCobrancaIndividual() {
    var msg = gerarMsgCobranca();
    if (navigator.clipboard) {
        navigator.clipboard.writeText(msg).then(function() {
            var t = document.createElement('div');
            t.textContent = '✓ Mensagem copiada! Cole no WhatsApp.';
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;'
                            + 'padding:12px 20px;border-radius:8px;font-size:14px;z-index:9999;'
                            + 'box-shadow:0 4px 16px rgba(0,0,0,.3);transition:opacity .4s';
            document.body.appendChild(t);
            setTimeout(function() { t.style.opacity = '0'; setTimeout(function() { t.remove(); }, 400); }, 3000);
        });
    } else {
        prompt('Copie a mensagem:', msg);
    }
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

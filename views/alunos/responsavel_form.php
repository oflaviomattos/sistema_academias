<?php
$editando  = !empty($responsavel['id']);
$pageTitle = $editando ? 'Editar Responsavel' : 'Novo Responsavel';
require_once __DIR__ . '/../layouts/header.php';
$r = $responsavel;
$alunosVinculados  = $editando ? $alunosVinculados  : [];
$todosAlunos       = $editando ? $todosAlunos       : [];
$idsVinculados = array_column($alunosVinculados, 'id');
?>

<div style="display:grid;grid-template-columns:1fr <?= $editando ? '1fr' : '' ?>;gap:20px;align-items:start">

  <!-- DADOS DO RESPONSAVEL -->
  <div class="card">
    <div class="card-header">
      <span>👨‍👩‍👧</span><h2><?= $pageTitle ?></h2>
      <a href="<?= BASE_URL ?>/?page=responsaveis" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
    </div>
    <div class="card-body">
      <form method="POST" action="<?= BASE_URL ?>/?page=<?= $editando ? 'responsaveis.atualizar' : 'responsaveis.salvar' ?>">
        <?php if ($editando): ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><?php endif; ?>

        <div class="form-group">
          <label>Nome *</label>
          <input type="text" name="nome" class="form-control" required
                 value="<?= h($r['nome'] ?? '') ?>" placeholder="Nome completo">
        </div>
        <div class="form-group">
          <label>Telefone / WhatsApp *</label>
          <input type="text" name="telefone" class="form-control" required
                 value="<?= h($r['telefone'] ?? '') ?>" placeholder="79 99999-9999">
        </div>
        <div class="form-group">
          <label>E-mail</label>
          <input type="email" name="email" class="form-control"
                 value="<?= h($r['email'] ?? '') ?>">
        </div>
        <div class="d-flex gap-8">
          <button type="submit" class="btn btn-primary">💾 Salvar</button>
          <a href="<?= BASE_URL ?>/?page=responsaveis" class="btn btn-outline">Cancelar</a>
        </div>
      </form>
    </div>
  </div>

  <?php if ($editando): ?>
  <!-- VINCULAR ALUNOS -->
  <div class="card">
    <div class="card-header"><span>👥</span><h2>Alunos vinculados</h2></div>
    <div class="card-body" style="padding:0">

      <!-- Alunos já vinculados -->
      <?php if (empty($alunosVinculados)): ?>
        <div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px">
          Nenhum aluno vinculado ainda.
        </div>
      <?php else: ?>
      <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9">
        <div style="font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
          Vinculados (<?= count($alunosVinculados) ?>)
        </div>
        <div style="display:flex;flex-direction:column;gap:6px">
          <?php foreach ($alunosVinculados as $a): ?>
          <div style="display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:8px 10px">
            <span class="faixa-badge faixa-<?= strtolower(explode('/',$a['faixa'])[0]) ?>">
              <?= h($a['faixa']) ?>
            </span>
            <span style="flex:1;font-size:13.5px;font-weight:500"><?= h($a['nome_completo']) ?></span>
            <?php
            $sn = $a['serie_nivel'] ?? '';
            if (strpos($sn, 'Infantil ') === 0) {
                echo '<span class="text-muted text-small">Inf' . substr($sn, 9) . '</span>';
            } elseif (strpos($sn, 'Fund I ') === 0) {
                echo '<span class="text-muted text-small">FI' . substr($sn, 7) . 'º</span>';
            } elseif (strpos($sn, 'Fund II ') === 0) {
                echo '<span class="text-muted text-small">FII' . substr($sn, 8) . 'º</span>';
            } elseif ($sn) {
                echo '<span class="text-muted text-small">' . h($sn) . '</span>';
            }
            ?>
            <a href="<?= APP_URL ?>/index.php?page=responsaveis.desvincular&aluno_id=<?= $a['id'] ?>&responsavel_id=<?= $r['id'] ?>"
               class="btn btn-danger btn-xs"
               data-confirm="Desvincular <?= h($a['nome_completo']) ?> deste responsavel?"
               title="Desvincular">✕</a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Busca para vincular novos -->
      <div style="padding:12px 16px">
        <div style="font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
          Vincular aluno
        </div>
        <div style="position:relative">
          <input type="text" id="busca-aluno-vinc"
                 class="form-control"
                 placeholder="🔍 Digite o nome do aluno..."
                 autocomplete="off"
                 onkeyup="buscarParaVincular(this.value)">
          <div id="resultado-vinc"
               style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #d1d5db;border-radius:0 0 6px 6px;box-shadow:0 4px 12px rgba(0,0,0,.1);z-index:100;max-height:200px;overflow-y:auto">
          </div>
        </div>
      </div>

    </div>
  </div>
  <?php endif; ?>

</div>

<?php if ($editando): ?>
<script>
var responsavelId = <?= (int)$r['id'] ?>;
var apiUrl = '<?= APP_URL ?>/api/alunos.php';
var vincularUrl = '<?= APP_URL ?>/index.php?page=responsaveis.vincular';
var timer = null;

function buscarParaVincular(q) {
    clearTimeout(timer);
    if (q.length < 1) {
        document.getElementById('resultado-vinc').style.display = 'none';
        return;
    }
    timer = setTimeout(function() {
        fetch(apiUrl + '?q=' + encodeURIComponent(q))
        .then(function(r){ return r.json(); })
        .then(function(alunos) {
            var div = document.getElementById('resultado-vinc');
            if (!alunos.length) {
                div.innerHTML = '<div style="padding:12px 14px;font-size:13px;color:#94a3b8">Nenhum aluno encontrado</div>';
                div.style.display = 'block';
                return;
            }
            // IDs já vinculados
            var vinculados = <?= json_encode(array_map('intval', $idsVinculados)) ?>;
            var html = '';
            alunos.forEach(function(a) {
                var jaVinc = vinculados.indexOf(parseInt(a.id)) !== -1;
                html += '<div style="display:flex;align-items:center;gap:8px;padding:9px 14px;border-bottom:1px solid #f1f5f9;font-size:13px' + (jaVinc ? ';opacity:.4;pointer-events:none' : ';cursor:pointer') + '"'
                      + (jaVinc ? '' : ' onclick="vincular(' + a.id + ')"'
                                     + ' onmouseover="this.style.background=\'#f0fdf4\'"'
                                     + ' onmouseout="this.style.background=\'\'"')
                      + '>'
                      + '<span style="flex:1;font-weight:500">' + a.nome_completo + '</span>'
                      + '<span style="font-size:11.5px;color:#64748b">' + a.faixa + '</span>'
                      + (jaVinc ? '<span style="font-size:11px;color:#94a3b8">já vinculado</span>' : '<span style="font-size:11px;color:#16a34a">+ vincular</span>')
                      + '</div>';
            });
            div.innerHTML = html;
            div.style.display = 'block';
        });
    }, 250);
}

function vincular(alunoId) {
    window.location = vincularUrl + '&aluno_id=' + alunoId + '&responsavel_id=' + responsavelId;
}

// Fecha ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('#busca-aluno-vinc') && !e.target.closest('#resultado-vinc')) {
        document.getElementById('resultado-vinc').style.display = 'none';
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

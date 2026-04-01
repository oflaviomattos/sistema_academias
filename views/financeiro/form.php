<?php $pageTitle = 'Lancar Mensalidade'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card" style="max-width:580px">
  <div class="card-header">
    <span>💳</span><h2>Lancar Mensalidade</h2>
    <?php if (!empty($_GET['aluno_id'])): ?>
      <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= (int)$_GET['aluno_id'] ?>" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?page=financeiro.salvar">

      <!-- BUSCA AJAX DE ALUNO -->
      <div class="form-group">
        <label>Aluno *</label>
        <div style="position:relative">
          <input type="text" id="busca-aluno"
                 class="form-control"
                 placeholder="🔍 Digite as primeiras letras do nome..."
                 autocomplete="off"
                 onkeyup="buscarAluno(this.value)"
                 <?= !empty($_GET['aluno_id']) ? 'style="display:none"' : '' ?>>

          <input type="hidden" name="aluno_id" id="aluno_id"
                 value="<?= (int)($_GET['aluno_id'] ?? 0) ?>">

          <!-- Card do aluno selecionado -->
          <div id="aluno-selecionado" style="<?= empty($_GET['aluno_id']) ? 'display:none' : '' ?>background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:10px 14px;display:flex;align-items:center;gap:10px">
            <span id="aluno-nome" style="flex:1;font-weight:600;font-size:14px">
              <?php
                if (!empty($_GET['aluno_id'])) {
                    foreach ($alunos as $a) {
                        if ($a['id'] == $_GET['aluno_id']) {
                            echo h($a['nome_completo']) . ' — ' . h($a['faixa']);
                            break;
                        }
                    }
                }
              ?>
            </span>
            <button type="button" onclick="limparAluno()"
                    style="background:none;border:none;cursor:pointer;color:#64748b;font-size:16px"
                    title="Trocar aluno">✕</button>
          </div>

          <!-- Dropdown de resultados -->
          <div id="resultado-aluno"
               style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #d1d5db;border-radius:0 0 8px 8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:200;max-height:260px;overflow-y:auto">
          </div>
        </div>
        <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block">
          Digite ao menos 1 letra para buscar
        </span>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Mes de Referencia *</label>
          <input type="month" name="mes_referencia" class="form-control" required
                 value="<?= date('Y-m') ?>">
        </div>
        <div class="form-group">
          <label>Valor (R$) *</label>
          <input type="text" name="valor" class="form-control" required
                 placeholder="0,00" value="0,00">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Data de Vencimento *</label>
          <input type="date" name="data_vencimento" class="form-control" required
                 value="<?= date('Y-m-10') ?>">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="pendente">⏳ Pendente</option>
            <option value="pago">✅ Pago</option>
            <option value="atrasado">🔴 Atrasado</option>
            <option value="integral">🟢 Integral (bolsa)</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Forma de Pagamento</label>
          <select name="forma_pagamento" class="form-control">
            <option value="">—</option>
            <?php foreach (FORMAS_PAGAMENTO as $f): ?>
            <option value="<?= $f ?>"><?= $f ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Data do Pagamento</label>
          <input type="date" name="data_pagamento" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label>Observacoes</label>
        <input type="text" name="observacoes" class="form-control" placeholder="ex: Pix 02/03">
      </div>

      <div class="d-flex gap-8">
        <button type="submit" class="btn btn-success" id="btn-submit" <?= empty($_GET['aluno_id']) ? 'disabled' : '' ?>>
          ✅ Lancar
        </button>
        <?php if (!empty($_GET['aluno_id'])): ?>
          <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= (int)$_GET['aluno_id'] ?>" class="btn btn-outline">Cancelar</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline">Cancelar</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<script>
var apiUrl = '<?= APP_URL ?>/api/alunos.php';
var timer  = null;

function buscarAluno(q) {
    clearTimeout(timer);
    var div = document.getElementById('resultado-aluno');
    if (q.length < 1) { div.style.display = 'none'; return; }

    timer = setTimeout(function() {
        fetch(apiUrl + '?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(alunos) {
            if (!alunos.length) {
                div.innerHTML = '<div style="padding:14px;font-size:13px;color:#94a3b8">Nenhum aluno encontrado</div>';
                div.style.display = 'block';
                return;
            }
            var html = '';
            alunos.forEach(function(a) {
                var sn = a.serie_nivel || '';
                var serieLabel = '';
                if (sn.indexOf('Infantil ') === 0) serieLabel = 'Inf' + sn.substring(9);
                else if (sn.indexOf('Fund I ') === 0) serieLabel = 'FI' + sn.substring(7) + 'º';
                else if (sn.indexOf('Fund II ') === 0) serieLabel = 'FII' + sn.substring(8) + 'º';
                else if (sn) serieLabel = sn;
                var info = a.faixa + (serieLabel ? ' · ' + serieLabel : '') + (a.academia_nome ? ' · ' + a.academia_nome : '');
                html += '<div style="padding:10px 16px;cursor:pointer;border-bottom:1px solid #f1f5f9"'
                      + ' onclick="selecionarAluno(' + a.id + ', \'' + a.nome_completo.replace(/'/g,"\\'") + '\', \'' + a.faixa + '\')"'
                      + ' onmouseover="this.style.background=\'#f0fdf4\'"'
                      + ' onmouseout="this.style.background=\'\'">'
                      + '<div style="font-weight:600;font-size:13.5px">' + a.nome_completo + '</div>'
                      + '<div style="font-size:12px;color:#64748b;margin-top:1px">' + info + '</div>'
                      + '</div>';
            });
            div.innerHTML = html;
            div.style.display = 'block';
        });
    }, 200);
}

function selecionarAluno(id, nome, faixa) {
    document.getElementById('aluno_id').value = id;
    document.getElementById('aluno-nome').textContent = nome + ' — ' + faixa;
    document.getElementById('aluno-selecionado').style.display = 'flex';
    document.getElementById('busca-aluno').style.display = 'none';
    document.getElementById('resultado-aluno').style.display = 'none';
    document.getElementById('btn-submit').disabled = false;
}

function limparAluno() {
    document.getElementById('aluno_id').value = '';
    document.getElementById('busca-aluno').value = '';
    document.getElementById('busca-aluno').style.display = 'block';
    document.getElementById('busca-aluno').focus();
    document.getElementById('aluno-selecionado').style.display = 'none';
    document.getElementById('btn-submit').disabled = true;
}

// Fecha dropdown ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('#busca-aluno') && !e.target.closest('#resultado-aluno')) {
        document.getElementById('resultado-aluno').style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php $pageTitle = 'Configuracoes'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">

  <!-- FORMULÁRIO PRINCIPAL -->
  <div class="card">
    <div class="card-header"><span>⚙️</span><h2>Configuracoes do Sistema</h2></div>
    <div class="card-body">
      <form method="POST" action="<?= BASE_URL ?>/?page=configuracoes.salvar">

        <!-- Chave PIX e nome do professor -->
        <div style="padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #f1f5f9">
          <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px">
            Dados para cobranca
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Chave PIX</label>
              <input type="text" name="chave_pix" class="form-control"
                     value="<?= h($chave_pix ?? '') ?>"
                     placeholder="ex: 79996480481">
              <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block">
                Aparece automaticamente na mensagem
              </span>
            </div>
            <div class="form-group">
              <label>Nome do professor / assinatura</label>
              <input type="text" name="nome_professor" class="form-control"
                     value="<?= h($nome_professor ?? '') ?>"
                     placeholder="ex: Prof. Marcus Uilson Correa">
            </div>
          </div>
        </div>

        <!-- Template da mensagem -->
        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px">
          Modelo de mensagem de cobranca
        </div>

        <div class="form-group">
          <label>Mensagem</label>
          <textarea name="mensagem_cobranca" id="msg-template" class="form-control"
                    rows="12" style="font-family:system-ui;font-size:14px;line-height:1.7;resize:vertical"
                    oninput="atualizarPreview()"><?= h($mensagem ?? '') ?></textarea>
          <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px">
            <span style="font-size:12px;color:#64748b;width:100%;margin-bottom:2px">
              Variaveis disponíveis (clique para inserir):
            </span>
            <?php
            $variaveis = [
                '{responsavel}'  => 'Nome do responsavel',
                '{aluno}'        => 'Nome do aluno',
                '{academia}'     => 'Nome da academia',
                '{mes}'          => 'Mes de referencia',
                '{vencimento}'   => 'Data de vencimento',
                '{valor}'        => 'Valor da mensalidade',
                '{valor_total}'  => 'Total em aberto',
                '{pix}'          => 'Chave PIX',
                '{professor}'    => 'Nome do professor',
            ];
            foreach ($variaveis as $var => $desc): ?>
            <button type="button"
                    onclick="inserirVariavel('<?= $var ?>')"
                    title="<?= $desc ?>"
                    style="background:#f1f5f9;border:1px solid #d1d5db;border-radius:4px;padding:3px 8px;font-size:12px;font-family:monospace;cursor:pointer;color:#374151"
                    onmouseover="this.style.background='#dbeafe'"
                    onmouseout="this.style.background='#f1f5f9'">
              <?= $var ?>
            </button>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">💾 Salvar Configuracoes</button>
      </form>
    </div>
  </div>

  <!-- PREVIEW E AJUDA -->
  <div style="display:flex;flex-direction:column;gap:20px">

    <!-- Preview da mensagem -->
    <div class="card">
      <div class="card-header"><span>👁️</span><h2>Preview</h2>
        <button onclick="copiarPreview()" class="btn btn-outline btn-sm ms-auto">📋 Copiar</button>
      </div>
      <div class="card-body" style="padding:0">
        <div id="preview-msg"
             style="padding:16px;font-size:13.5px;line-height:1.8;white-space:pre-wrap;background:#f0fdf4;border-radius:0 0 8px 8px;color:#1e293b;min-height:200px">
        </div>
      </div>
    </div>

    <!-- Variáveis com descrição -->
    <div class="card">
      <div class="card-header"><span>📖</span><h2>Guia de variaveis</h2></div>
      <div class="card-body" style="padding:0">
        <table style="font-size:12.5px">
          <thead><tr><th>Variavel</th><th>Substitui por</th></tr></thead>
          <tbody>
            <?php foreach ($variaveis as $var => $desc): ?>
            <tr>
              <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px"><?= $var ?></code></td>
              <td style="color:#64748b"><?= $desc ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mensagem original do professor (referência) -->
    <div class="card">
      <div class="card-header"><span>💡</span><h2>Mensagem original</h2></div>
      <div class="card-body"
           style="font-size:13px;line-height:1.8;color:#475569;white-space:pre-line;background:#fafafa;border-radius:8px;padding:14px">Bom dia familia!
Estou encaminhando este lembrete referente a parcela do Judo (colegio Jose Olino) que venceu dia 30 de janeiro no valor de R$140,00.
PIX: 79996480481 (Marcado Pago). Solicito que envie o comprovante para o controle. Qualquer duvida estou a disposicao.
Grato!
Prof. Marcus Uilson Correa</div>
    </div>

  </div>
</div>

<script>
// Preview em tempo real
var dadosExemplo = {
    '{responsavel}' : 'Familia Silva',
    '{aluno}'       : 'Joao Silva',
    '{academia}'    : 'Colegio Jose Olino',
    '{mes}'         : 'Janeiro/2026',
    '{vencimento}'  : '30 de janeiro',
    '{valor}'       : 'R$ 140,00',
    '{valor_total}' : 'R$ 140,00',
    '{pix}'         : '<?= h($chave_pix ?? '79996480481') ?>',
    '{professor}'   : '<?= h($nome_professor ?? 'Prof. Marcus Uilson Correa') ?>',
};

function atualizarPreview() {
    var msg = document.getElementById('msg-template').value;
    Object.keys(dadosExemplo).forEach(function(k) {
        msg = msg.split(k).join(dadosExemplo[k]);
    });
    document.getElementById('preview-msg').textContent = msg;
}

function inserirVariavel(variavel) {
    var ta = document.getElementById('msg-template');
    var pos = ta.selectionStart;
    var antes = ta.value.substring(0, pos);
    var depois = ta.value.substring(ta.selectionEnd);
    ta.value = antes + variavel + depois;
    ta.selectionStart = ta.selectionEnd = pos + variavel.length;
    ta.focus();
    atualizarPreview();
}

function copiarPreview() {
    var texto = document.getElementById('preview-msg').textContent;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(function() {
            mostrarAviso('Preview copiado!');
        });
    } else {
        prompt('Copie:', texto);
    }
}

function mostrarAviso(msg) {
    var t = document.createElement('div');
    t.textContent = '✓ ' + msg;
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;'
                    + 'padding:12px 20px;border-radius:8px;font-size:14px;z-index:9999';
    document.body.appendChild(t);
    setTimeout(function() { t.remove(); }, 2500);
}

// Atualiza preview ao carregar
atualizarPreview();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

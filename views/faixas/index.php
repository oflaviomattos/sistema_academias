<?php $pageTitle = 'Faixas'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start">

  <!-- LISTA DE FAIXAS (arrastar para reordenar) -->
  <div class="card">
    <div class="card-header">
      <span>🎽</span>
      <h2>Faixas <span class="badge badge-secondary"><?= count($faixas) ?></span></h2>
      <span style="font-size:12px;color:#94a3b8;margin-left:8px">arraste para reordenar</span>
    </div>

    <?php if (empty($faixas)): ?>
      <div class="empty-state">
        <div class="icon">🎽</div>
        <h3>Nenhuma faixa cadastrada</h3>
        <p>Adicione a primeira faixa ao lado.</p>
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:36px"></th>
            <th style="width:36px">Ordem</th>
            <th>Nome</th>
            <th style="width:80px">Cor</th>
            <th style="width:80px">Preview</th>
            <th style="width:100px">Acoes</th>
          </tr>
        </thead>
        <tbody id="faixas-lista">
          <?php foreach ($faixas as $f): ?>
          <tr data-id="<?= $f['id'] ?>" style="cursor:grab">
            <td style="color:#94a3b8;font-size:16px;text-align:center">⠿</td>
            <td style="text-align:center;color:#64748b"><?= $f['ordem'] ?></td>
            <td>
              <strong><?= h($f['nome']) ?></strong>
            </td>
            <td>
              <span style="font-size:12px;font-family:monospace;color:#64748b"><?= h($f['cor_hex']) ?></span>
            </td>
            <td>
              <span style="display:inline-block;padding:3px 10px;border-radius:4px;font-size:12px;font-weight:600;
                           background:<?= h($f['cor_hex']) ?>;
                           color:<?= isColorDark($f['cor_hex']) ? '#fff' : '#1e293b' ?>;
                           border:1px solid rgba(0,0,0,.12)">
                <?= h($f['nome']) ?>
              </span>
            </td>
            <td>
              <div class="d-flex gap-8">
                <button onclick="editarFaixa(<?= $f['id'] ?>, '<?= h($f['nome']) ?>', '<?= $f['cor_hex'] ?>', <?= $f['ordem'] ?>)"
                        class="btn btn-outline btn-xs" title="Editar">✏️</button>
                <a href="<?= BASE_URL ?>/?page=faixas.excluir&id=<?= $f['id'] ?>"
                   class="btn btn-danger btn-xs"
                   data-confirm="Excluir a faixa '<?= h($f['nome']) ?>'?"
                   title="Excluir">🗑</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- FORMULÁRIO DIREITO -->
  <div style="display:flex;flex-direction:column;gap:20px">

    <!-- Adicionar nova faixa -->
    <div class="card">
      <div class="card-header"><span id="form-icon">➕</span><h2 id="form-titulo">Nova Faixa</h2></div>
      <div class="card-body">
        <form method="POST" id="form-faixa" action="<?= BASE_URL ?>/?page=faixas.salvar">
          <input type="hidden" name="id" id="faixa-id" value="">

          <div class="form-group">
            <label>Nome da faixa *</label>
            <input type="text" name="nome" id="faixa-nome" class="form-control" required
                   placeholder="ex: branca com ponta azul">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Cor</label>
              <div style="display:flex;gap:8px;align-items:center">
                <input type="color" name="cor_hex" id="faixa-cor"
                       value="#cccccc"
                       style="width:48px;height:36px;padding:2px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer">
                <input type="text" id="faixa-cor-hex"
                       value="#cccccc"
                       class="form-control"
                       style="flex:1;font-family:monospace"
                       maxlength="7"
                       oninput="syncCorHex(this.value)"
                       placeholder="#cccccc">
              </div>
            </div>
            <div class="form-group">
              <label>Ordem</label>
              <input type="number" name="ordem" id="faixa-ordem" class="form-control"
                     value="<?= count($faixas) + 1 ?>" min="1" max="99">
            </div>
          </div>

          <!-- Preview da faixa -->
          <div class="form-group">
            <label>Preview</label>
            <div id="preview-faixa"
                 style="display:inline-block;padding:5px 16px;border-radius:4px;font-size:13px;font-weight:600;background:#cccccc;border:1px solid rgba(0,0,0,.12)">
              Nova faixa
            </div>
          </div>

          <div class="d-flex gap-8">
            <button type="submit" class="btn btn-primary" id="btn-salvar">➕ Adicionar</button>
            <button type="button" onclick="cancelarEdicao()" id="btn-cancelar"
                    class="btn btn-outline" style="display:none">Cancelar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Dicas -->
    <div class="card">
      <div class="card-header"><span>💡</span><h2>Dicas</h2></div>
      <div class="card-body" style="font-size:13px;color:#475569;line-height:1.8">
        <div>• Arraste as linhas para definir a ordem de progressao</div>
        <div>• A cor escolhida aparece no badge dos alunos</div>
        <div>• Faixas em uso por alunos nao podem ser excluidas</div>
        <div>• Use nomes como: <em>branca</em>, <em>branca/cinza</em>, <em>cinza</em>...</div>
      </div>
    </div>

  </div>
</div>

<script>
// ── Preview de cor em tempo real ──────────────────────────
var corInput = document.getElementById('faixa-cor');
var nomeInput = document.getElementById('faixa-nome');

corInput.addEventListener('input', function() {
    atualizarPreview(this.value);
    document.getElementById('faixa-cor-hex').value = this.value;
});
nomeInput.addEventListener('input', atualizarPreviewTexto);

function syncCorHex(hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        corInput.value = hex;
        atualizarPreview(hex);
    }
}

function atualizarPreview(cor) {
    var prev = document.getElementById('preview-faixa');
    prev.style.background = cor;
    prev.style.color = isEscuro(cor) ? '#fff' : '#1e293b';
}

function atualizarPreviewTexto() {
    var prev = document.getElementById('preview-faixa');
    prev.textContent = this.value || 'Nova faixa';
}
nomeInput.addEventListener('input', atualizarPreviewTexto);

function isEscuro(hex) {
    var r = parseInt(hex.slice(1,3),16);
    var g = parseInt(hex.slice(3,5),16);
    var b = parseInt(hex.slice(5,7),16);
    return (0.299*r + 0.587*g + 0.114*b) < 140;
}

// ── Editar faixa ──────────────────────────────────────────
function editarFaixa(id, nome, cor, ordem) {
    document.getElementById('form-faixa').action = '<?= BASE_URL ?>/?page=faixas.atualizar';
    document.getElementById('form-icon').textContent = '✏️';
    document.getElementById('form-titulo').textContent = 'Editar Faixa';
    document.getElementById('btn-salvar').textContent = '💾 Salvar';
    document.getElementById('btn-cancelar').style.display = '';
    document.getElementById('faixa-id').value = id;
    document.getElementById('faixa-nome').value = nome;
    document.getElementById('faixa-cor').value = cor;
    document.getElementById('faixa-cor-hex').value = cor;
    document.getElementById('faixa-ordem').value = ordem;
    atualizarPreview(cor);
    document.getElementById('preview-faixa').textContent = nome;
    document.getElementById('faixa-nome').focus();
    window.scrollTo({top:0, behavior:'smooth'});
}

function cancelarEdicao() {
    document.getElementById('form-faixa').action = '<?= BASE_URL ?>/?page=faixas.salvar';
    document.getElementById('form-icon').textContent = '➕';
    document.getElementById('form-titulo').textContent = 'Nova Faixa';
    document.getElementById('btn-salvar').textContent = '➕ Adicionar';
    document.getElementById('btn-cancelar').style.display = 'none';
    document.getElementById('faixa-id').value = '';
    document.getElementById('faixa-nome').value = '';
    document.getElementById('faixa-cor').value = '#cccccc';
    document.getElementById('faixa-cor-hex').value = '#cccccc';
    document.getElementById('preview-faixa').textContent = 'Nova faixa';
    document.getElementById('preview-faixa').style.background = '#cccccc';
}

// ── Drag-and-drop para reordenar ──────────────────────────
var tbody = document.getElementById('faixas-lista');
if (tbody) {
    var dragging = null;

    tbody.querySelectorAll('tr').forEach(function(row) {
        row.draggable = true;
        row.addEventListener('dragstart', function() {
            dragging = this;
            setTimeout(function() { dragging.style.opacity = '0.4'; }, 0);
        });
        row.addEventListener('dragend', function() {
            this.style.opacity = '';
            salvarOrdem();
        });
        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            var rect = this.getBoundingClientRect();
            var meio = rect.top + rect.height / 2;
            if (dragging && dragging !== this) {
                if (e.clientY < meio) tbody.insertBefore(dragging, this);
                else tbody.insertBefore(dragging, this.nextSibling);
            }
        });
    });

    function salvarOrdem() {
        var ids = Array.from(tbody.querySelectorAll('tr')).map(function(tr) {
            return parseInt(tr.dataset.id);
        });
        fetch('<?= APP_URL ?>/index.php?page=faixas.reordenar', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(ids)
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

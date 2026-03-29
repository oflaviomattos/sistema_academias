<?php
$pageTitle  = 'Importar CSV';
$resultado  = $_SESSION['import_resultado'] ?? null;
$tipoImport = $_SESSION['import_tipo'] ?? null;
unset($_SESSION['import_resultado'], $_SESSION['import_tipo']);
require_once __DIR__ . '/../layouts/header.php';
?>

<?php if ($resultado): ?>
<div class="card" style="margin-bottom:20px;border-left:4px solid <?= empty($resultado['erros']) ? '#16a34a' : '#d97706' ?>">
  <div class="card-body">
    <h3 style="margin-bottom:12px">📊 Resultado da Importação</h3>
    <div class="stats-grid" style="margin-bottom:0">
      <div class="stat-card green">
        <div class="label">Criados</div>
        <div class="value"><?= $resultado['criados'] ?></div>
      </div>
      <div class="stat-card blue">
        <div class="label">Atualizados</div>
        <div class="value"><?= $resultado['atualizados'] ?></div>
      </div>
      <div class="stat-card <?= empty($resultado['erros']) ? 'blue' : 'red' ?>">
        <div class="label">Erros</div>
        <div class="value"><?= count($resultado['erros']) ?></div>
      </div>
      <div class="stat-card blue">
        <div class="label">Linhas processadas</div>
        <div class="value"><?= $resultado['linhas'] ?></div>
      </div>
    </div>
    <?php if (!empty($resultado['erros'])): ?>
    <div style="margin-top:16px;background:#fff5f5;border:1px solid #fecaca;border-radius:6px;padding:14px;max-height:200px;overflow-y:auto">
      <strong style="font-size:13px;color:#991b1b">Avisos / Erros:</strong>
      <ul style="margin-top:8px;padding-left:20px;font-size:12.5px;color:#7f1d1d">
        <?php foreach ($resultado['erros'] as $e): ?>
        <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

  <!-- FORM: Importar Alunos -->
  <div class="card">
    <div class="card-header"><span>👥</span><h2>Importar Alunos</h2></div>
    <div class="card-body">
      <form method="POST" action="<?= BASE_URL ?>/?page=importacao.upload" enctype="multipart/form-data">
        <input type="hidden" name="tipo" value="alunos">
        <div class="form-group">
          <label>Academia de destino *</label>
          <select name="academia_id" class="form-control" required>
            <option value="">Selecione...</option>
            <?php foreach ($academias as $ac): ?>
            <option value="<?= $ac['id'] ?>"><?= h($ac['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Arquivo CSV *</label>
          <input type="file" name="csv" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          📥 Importar Alunos
        </button>
      </form>

      <hr style="margin:20px 0;border:none;border-top:1px solid #f1f5f9">
      <h4 style="font-size:13px;font-weight:600;margin-bottom:10px">📋 Colunas esperadas no CSV:</h4>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Coluna no CSV</th><th>Campo no sistema</th><th>Obrigatório</th></tr></thead>
          <tbody>
            <tr><td><code>aluno</code></td><td>Nome completo</td><td>✅</td></tr>
            <tr><td><code>manha / t</code></td><td>Turno (M/T/N)</td><td>—</td></tr>
            <tr><td><code>contrato</code></td><td>Contrato OK (ok/vazio)</td><td>—</td></tr>
            <tr><td><code>#</code></td><td>Série / nível</td><td>—</td></tr>
            <tr><td><code>faixa</code></td><td>Faixa</td><td>—</td></tr>
            <tr><td><code>tamanho</code></td><td>Tamanho kimono</td><td>—</td></tr>
            <tr><td><code>responsavel</code></td><td>Nome do responsável</td><td>—</td></tr>
            <tr><td><code>contato</code></td><td>Telefone responsável</td><td>—</td></tr>
          </tbody>
        </table>
      </div>
      <p class="text-muted text-small" style="margin-top:8px">
        Aceita separador vírgula ou ponto-e-vírgula.<br>
        Alunos já existentes (mesmo nome + academia) serão <strong>atualizados</strong>.
      </p>

      <div style="margin-top:16px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px">
        <div style="font-size:13px;font-weight:600;margin-bottom:8px">📎 CSV de exemplo</div>
        <p style="font-size:12px;color:#64748b;margin-bottom:10px">
          Baixe o modelo pronto com dados de exemplo baseados na planilha atual.
          Edite no Excel ou Google Sheets e importe.
        </p>
        <a href="<?= APP_URL ?>/index.php?page=exemplos&tipo=alunos"
           class="btn btn-success btn-sm" style="width:100%;justify-content:center">
          ⬇️ Baixar exemplo_alunos.csv
        </a>
      </div>
    </div>
  </div>

  <!-- FORM: Importar Mensalidades -->
  <div class="card">
    <div class="card-header"><span>💰</span><h2>Importar Mensalidades</h2></div>
    <div class="card-body">
      <div class="alert alert-warning" style="margin-bottom:16px">
        ⚠️ Importe os <strong>alunos primeiro</strong> antes de importar mensalidades.
      </div>
      <form method="POST" action="<?= BASE_URL ?>/?page=importacao.upload" enctype="multipart/form-data">
        <input type="hidden" name="tipo" value="mensalidades">
        <div class="form-group">
          <label>Academia *</label>
          <select name="academia_id" class="form-control" required>
            <option value="">Selecione...</option>
            <?php foreach ($academias as $ac): ?>
            <option value="<?= $ac['id'] ?>"><?= h($ac['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Ano de referência *</label>
            <select name="ano" class="form-control">
              <?php for ($y=date('Y');$y>=2020;$y--): ?>
              <option value="<?= $y ?>" <?= $y==date('Y')?'selected':'' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Valor padrão (R$)</label>
            <input type="text" name="valor_padrao" class="form-control" placeholder="0,00">
            <span class="text-muted text-small">Usado quando o CSV não tem valor</span>
          </div>
        </div>
        <div class="form-group">
          <label>Arquivo CSV *</label>
          <input type="file" name="csv" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center">
          📥 Importar Mensalidades
        </button>
      </form>

      <hr style="margin:20px 0;border:none;border-top:1px solid #f1f5f9">
      <h4 style="font-size:13px;font-weight:600;margin-bottom:10px">📋 Formato das colunas de mês:</h4>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Conteúdo da célula</th><th>Interpretado como</th></tr></thead>
          <tbody>
            <tr><td><code>Pix 02/03</code></td><td>✅ Pago via Pix em 02/03</td></tr>
            <tr><td><code>Pix 27/02</code></td><td>✅ Pago via Pix em 27/02</td></tr>
            <tr><td><code>Pendente</code></td><td>⏳ Pendente</td></tr>
            <tr><td><code>ATRASADO</code></td><td>🔴 Atrasado</td></tr>
            <tr><td><code>integral</code></td><td>🟢 Bolsa integral</td></tr>
            <tr><td><code>Quitado</code></td><td>✅ Pago em dinheiro</td></tr>
            <tr><td><em>(vazia)</em></td><td>Ignorada</td></tr>
          </tbody>
        </table>
      </div>
      <p class="text-muted text-small" style="margin-top:8px">
        As colunas de meses são detectadas automaticamente pelos nomes:<br>
        <code>Jan, Fev, Mar, Abr, Mai, Jun, Jul, Ago, Set, Out, Nov, Dez</code>
      </p>

      <div style="margin-top:16px;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px">
        <div style="font-size:13px;font-weight:600;margin-bottom:8px">📎 CSV de exemplo</div>
        <p style="font-size:12px;color:#64748b;margin-bottom:10px">
          Modelo com os formatos exatos aceitos pelo sistema
          (<code>Pix 02/03</code>, <code>ATRASADO</code>, <code>integral</code>...).
        </p>
        <a href="<?= APP_URL ?>/index.php?page=exemplos&tipo=mensalidades"
           class="btn btn-success btn-sm" style="width:100%;justify-content:center">
          ⬇️ Baixar exemplo_mensalidades.csv
        </a>
      </div>
    </div>
  </div>

</div>

<!-- INSTRUÇÕES DE EXPORTAÇÃO DO GOOGLE SHEETS -->
<div class="card" style="margin-top:20px">
  <div class="card-header"><span>📗</span><h2>Como exportar do Google Sheets</h2></div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;font-size:13px">
      <div style="display:flex;gap:12px">
        <div style="font-size:24px;flex-shrink:0">1️⃣</div>
        <div>Abra a planilha no Google Sheets e selecione a aba desejada (ex: <strong>2026</strong>).</div>
      </div>
      <div style="display:flex;gap:12px">
        <div style="font-size:24px;flex-shrink:0">2️⃣</div>
        <div>Vá em <strong>Arquivo → Fazer download → Valores separados por vírgula (.csv)</strong>.</div>
      </div>
      <div style="display:flex;gap:12px">
        <div style="font-size:24px;flex-shrink:0">3️⃣</div>
        <div>O arquivo será baixado com o nome da aba. <strong>Não renomeie.</strong></div>
      </div>
      <div style="display:flex;gap:12px">
        <div style="font-size:24px;flex-shrink:0">4️⃣</div>
        <div>Importe primeiro os <strong>Alunos</strong>, depois as <strong>Mensalidades</strong> (com o mesmo CSV).</div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

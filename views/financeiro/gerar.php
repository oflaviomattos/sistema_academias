<?php $pageTitle = 'Gerar Mensalidades em Lote'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

  <!-- FORMULÁRIO -->
  <div class="card">
    <div class="card-header">
      <span>⚙️</span>
      <h2>Gerar Mensalidades em Lote</h2>
      <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
    </div>
    <div class="card-body">

      <form method="POST" action="<?= BASE_URL ?>/?page=financeiro.gerar">

        <div class="form-group">
          <label>Academia</label>
          <select name="academia_id" class="form-control" required>
            <option value="">Selecione a academia...</option>
            <?php foreach ($academias as $ac): ?>
            <option value="<?= $ac['id'] ?>"><?= h($ac['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Mês de Referência</label>
            <input type="month" name="mes_referencia" class="form-control" required
                   value="<?= date('Y-m') ?>">
          </div>
          <div class="form-group">
            <label>Valor (R$)</label>
            <input type="text" name="valor" class="form-control" required
                   placeholder="ex: 150,00" style="font-size:16px;font-weight:600">
          </div>
        </div>

        <div class="form-group">
          <label>Data de Vencimento</label>
          <input type="date" name="data_vencimento" class="form-control" required
                 value="<?= date('Y-m-10') ?>">
          <span style="font-size:12px;color:#94a3b8;margin-top:4px;display:block">
            Padrão: dia 10 do mês
          </span>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;font-size:15px">
          ⚙️ Gerar Mensalidades
        </button>

      </form>
    </div>
  </div>

  <!-- PAINEL DIREITO: COMO FUNCIONA + AÇÕES EXTRAS -->
  <div style="display:flex;flex-direction:column;gap:20px">

    <!-- Como funciona -->
    <div class="card">
      <div class="card-header"><span>ℹ️</span><h2>Como funciona</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

        <div style="display:flex;gap:14px;align-items:flex-start">
          <div style="width:32px;height:32px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#1d4ed8;flex-shrink:0">1</div>
          <div>
            <div style="font-weight:600;font-size:13.5px">Selecione a academia</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:2px">Todos os alunos <strong>ativos</strong> dessa unidade receberão o lançamento</div>
          </div>
        </div>

        <div style="display:flex;gap:14px;align-items:flex-start">
          <div style="width:32px;height:32px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#1d4ed8;flex-shrink:0">2</div>
          <div>
            <div style="font-weight:600;font-size:13.5px">Defina o mês e o valor</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:2px">Uma mensalidade <strong>pendente</strong> será criada para cada aluno</div>
          </div>
        </div>

        <div style="display:flex;gap:14px;align-items:flex-start">
          <div style="width:32px;height:32px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#15803d;flex-shrink:0">✓</div>
          <div>
            <div style="font-weight:600;font-size:13.5px">Duplicatas ignoradas automaticamente</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:2px">Alunos que já têm lançamento nesse mês <strong>não são afetados</strong></div>
          </div>
        </div>

        <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:6px;padding:10px 14px;font-size:12.5px;color:#713f12">
          💡 <strong>Dica:</strong> Gere primeiro, depois registre os pagamentos individualmente em <a href="<?= BASE_URL ?>/?page=financeiro" style="color:#1a56db">Mensalidades</a>.
        </div>
      </div>
    </div>

    <!-- Atalhos -->
    <div class="card">
      <div class="card-header"><span>⚡</span><h2>Ações rápidas</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
        <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline" style="justify-content:center">
          💰 Ver todas as mensalidades
        </a>
        <a href="<?= BASE_URL ?>/?page=financeiro.lancar" class="btn btn-outline" style="justify-content:center">
          ➕ Lançar mensalidade individual
        </a>
        <a href="<?= BASE_URL ?>/?page=alunos" class="btn btn-outline" style="justify-content:center">
          👥 Gerenciar alunos
        </a>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

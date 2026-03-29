<?php $pageTitle = 'Registrar Pagamento'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card" style="max-width:480px">
  <div class="card-header"><span>💰</span><h2>Registrar Pagamento</h2>
    <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <?php if (!$mensalidade): ?>
      <div class="alert alert-danger">Mensalidade não encontrada.</div>
    <?php else: ?>
    <div class="alert alert-info" style="margin-bottom:20px">
      <div>
        <strong><?= h($mensalidade['nome_completo']) ?></strong><br>
        <span class="text-muted"><?= mesReferencia($mensalidade['mes_referencia']) ?></span>
        — <strong><?= formatMoeda($mensalidade['valor']) ?></strong>
      </div>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/?page=financeiro.pagar">
      <input type="hidden" name="id" value="<?= $mensalidade['id'] ?>">
      <input type="hidden" name="mes" value="<?= $mensalidade['mes_referencia'] ?>">
      <div class="form-group">
        <label>Forma de Pagamento *</label>
        <select name="forma_pagamento" class="form-control" required>
          <?php foreach (FORMAS_PAGAMENTO as $f): ?>
          <option value="<?= $f ?>"><?= $f ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Data do Pagamento *</label>
        <input type="date" name="data_pagamento" class="form-control" required value="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-group">
        <label>Observações</label>
        <input type="text" name="observacoes" class="form-control" placeholder="ex: Pix confirmado">
      </div>
      <button type="submit" class="btn btn-success" style="width:100%;justify-content:center">✅ Confirmar Pagamento</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

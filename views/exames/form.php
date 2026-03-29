<?php $pageTitle = 'Agendar Exame de Faixa'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card" style="max-width:520px">
  <div class="card-header"><span>🎽</span><h2>Agendar Exame de Faixa</h2>
    <a href="<?= BASE_URL ?>/?page=exames" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?page=exames.salvar">
      <div class="form-group">
        <label>Aluno *</label>
        <select name="aluno_id" class="form-control" required>
          <option value="">Selecione...</option>
          <?php foreach ($alunos as $a): ?>
          <option value="<?= $a['id'] ?>"
            <?= ($_GET['aluno_id']??'')==$a['id']?'selected':'' ?>>
            <?= h($a['nome_completo']) ?> — <?= h($a['faixa']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Faixa Atual</label>
          <select name="faixa_atual" class="form-control">
            <?php foreach (FAIXAS as $f): ?>
            <option value="<?= $f ?>"><?= ucfirst($f) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nova Faixa</label>
          <select name="nova_faixa" class="form-control">
            <?php foreach (FAIXAS as $f): ?>
            <option value="<?= $f ?>"><?= ucfirst($f) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Data do Exame *</label>
        <input type="date" name="data_exame" class="form-control" required value="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-group">
        <label>Observações</label>
        <textarea name="observacoes" class="form-control" rows="2"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">✅ Agendar</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

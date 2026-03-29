<?php
$editando  = !empty($academia['id']);
$pageTitle = $editando ? 'Editar Academia' : 'Nova Academia';
require_once __DIR__ . '/../layouts/header.php';
$ac = $academia;
?>
<div class="card" style="max-width:480px">
  <div class="card-header"><span>🏫</span><h2><?= $pageTitle ?></h2>
    <a href="<?= BASE_URL ?>/?page=academias" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?page=<?= $editando ? 'academias.atualizar' : 'academias.salvar' ?>">
      <?php if ($editando): ?><input type="hidden" name="id" value="<?= $ac['id'] ?>"><?php endif; ?>
      <div class="form-group">
        <label>Nome da Academia *</label>
        <input type="text" name="nome" class="form-control" required value="<?= h($ac['nome'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Cidade</label>
        <input type="text" name="cidade" class="form-control" value="<?= h($ac['cidade'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Responsável pela Unidade</label>
        <input type="text" name="responsavel" class="form-control" value="<?= h($ac['responsavel'] ?? '') ?>">
      </div>
      <?php if ($editando): ?>
      <div class="form-group">
        <label>Status</label>
        <select name="ativo" class="form-control">
          <option value="1" <?= ($ac['ativo']??1)==1?'selected':'' ?>>Ativa</option>
          <option value="0" <?= ($ac['ativo']??1)==0?'selected':'' ?>>Inativa</option>
        </select>
      </div>
      <?php endif; ?>
      <div class="d-flex gap-8">
        <button type="submit" class="btn btn-primary">💾 Salvar</button>
        <a href="<?= BASE_URL ?>/?page=academias" class="btn btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php
$editando  = !empty($campeonato['id']);
$pageTitle = $editando ? 'Editar Campeonato' : 'Novo Campeonato';
require_once __DIR__ . '/../layouts/header.php';
$c = $campeonato;
$inscritosIds = array_column($inscritos ?? [], 'aluno_id');
?>
<div class="card" style="max-width:700px">
  <div class="card-header"><span>🏆</span><h2><?= $pageTitle ?></h2>
    <a href="<?= BASE_URL ?>/?page=campeonatos" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?page=<?= $editando ? 'campeonatos.atualizar' : 'campeonatos.salvar' ?>">
      <?php if ($editando): ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><?php endif; ?>
      <div class="form-row">
        <div class="form-group" style="flex:2">
          <label>Nome do Campeonato *</label>
          <input type="text" name="nome" class="form-control" required value="<?= h($c['nome'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Data *</label>
          <input type="date" name="data" class="form-control" required value="<?= h($c['data'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Local</label>
        <input type="text" name="local" class="form-control" value="<?= h($c['local'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Descrição</label>
        <textarea name="descricao" class="form-control" rows="2"><?= h($c['descricao'] ?? '') ?></textarea>
      </div>

      <?php if ($editando && !empty($alunos)): ?>
      <div class="form-group">
        <label>Inscrever Alunos</label>
        <div style="border:1px solid #d1d5db;border-radius:6px;padding:12px;max-height:220px;overflow-y:auto;display:grid;gap:6px">
          <?php foreach ($alunos as $a): ?>
          <label class="form-check">
            <input type="checkbox" name="alunos[]" value="<?= $a['id'] ?>"
              <?= in_array($a['id'], $inscritosIds) ? 'checked disabled' : '' ?>>
            <?= h($a['nome_completo']) ?> — <span class="faixa-badge faixa-<?= $a['faixa'] ?>"><?= h($a['faixa']) ?></span>
            <?= in_array($a['id'], $inscritosIds) ? '<span class="badge badge-success" style="margin-left:4px">Inscrito</span>' : '' ?>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="text-muted text-small" style="margin-top:4px">Alunos já inscritos são mostrados marcados e não podem ser desmarcados.</div>
      </div>
      <?php endif; ?>

      <div class="d-flex gap-8">
        <button type="submit" class="btn btn-primary">💾 Salvar</button>
        <a href="<?= BASE_URL ?>/?page=campeonatos" class="btn btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

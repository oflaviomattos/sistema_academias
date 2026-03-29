<?php $pageTitle = 'Academias'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card">
  <div class="card-header"><span>🏫</span><h2>Academias</h2>
    <a href="<?= BASE_URL ?>/?page=academias.criar" class="btn btn-primary btn-sm ms-auto">+ Nova Academia</a>
  </div>
  <?php if (empty($academias)): ?>
    <div class="empty-state"><div class="icon">🏫</div><h3>Nenhuma academia cadastrada</h3></div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nome</th><th>Cidade</th><th>Responsável</th><th>Status</th><th>Ações</th></tr></thead>
      <tbody>
      <?php foreach ($academias as $ac): ?>
        <tr>
          <td class="fw-600"><?= h($ac['nome']) ?></td>
          <td><?= h($ac['cidade'] ?? '—') ?></td>
          <td><?= h($ac['responsavel'] ?? '—') ?></td>
          <td><?= $ac['ativo'] ? '<span class="badge badge-success">Ativa</span>' : '<span class="badge badge-secondary">Inativa</span>' ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?page=academias.editar&id=<?= $ac['id'] ?>" class="btn btn-outline btn-xs">✏️ Editar</a>
            <a href="<?= BASE_URL ?>/?page=academias.excluir&id=<?= $ac['id'] ?>"
               class="btn btn-danger btn-xs" data-confirm="Excluir '<?= h($ac['nome']) ?>'?">🗑</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php $pageTitle = 'Campeonatos'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card">
  <div class="card-header"><span>🏆</span><h2>Campeonatos</h2>
    <a href="<?= BASE_URL ?>/?page=campeonatos.criar" class="btn btn-primary btn-sm ms-auto">+ Novo Campeonato</a>
  </div>
  <?php if (empty($campeonatos)): ?>
    <div class="empty-state"><div class="icon">🏆</div><h3>Nenhum campeonato cadastrado</h3></div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nome</th><th>Data</th><th>Local</th><th>Inscritos</th><th>Ações</th></tr></thead>
      <tbody>
      <?php foreach ($campeonatos as $c): ?>
        <tr>
          <td class="fw-600"><?= h($c['nome']) ?></td>
          <td><?= formatData($c['data']) ?></td>
          <td><?= h($c['local'] ?? '—') ?></td>
          <td><span class="badge badge-info"><?= $c['total_inscritos'] ?></span></td>
          <td>
            <a href="<?= BASE_URL ?>/?page=campeonatos.editar&id=<?= $c['id'] ?>" class="btn btn-outline btn-xs">✏️ Editar / Inscrever</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

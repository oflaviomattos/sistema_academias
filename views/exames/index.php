<?php $pageTitle = 'Exames de Faixa'; require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="card">
  <div class="card-header"><span>🎽</span><h2>Exames de Faixa</h2>
    <a href="<?= BASE_URL ?>/?page=exames.criar" class="btn btn-primary btn-sm ms-auto">+ Agendar Exame</a>
  </div>
  <?php if (empty($exames)): ?>
    <div class="empty-state"><div class="icon">🎽</div><h3>Nenhum exame registrado</h3></div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Aluno</th><th>Academia</th><th>Faixa Atual</th><th>Nova Faixa</th><th>Data</th><th>Status</th><th>Ações</th></tr></thead>
      <tbody>
      <?php foreach ($exames as $e): ?>
        <tr>
          <td class="fw-600"><?= h($e['nome_completo']) ?></td>
          <td class="text-muted text-small"><?= h($e['academia_nome']) ?></td>
          <td><span class="faixa-badge faixa-<?= strtolower($e['faixa_atual']) ?>"><?= h($e['faixa_atual']) ?></span></td>
          <td><span class="faixa-badge faixa-<?= strtolower($e['nova_faixa']) ?>"><?= h($e['nova_faixa']) ?></span></td>
          <td><?= formatData($e['data_exame']) ?></td>
          <td>
            <?php $bc=['aprovado'=>'success','reprovado'=>'danger','pendente'=>'warning'][$e['status']]??'secondary'; ?>
            <span class="badge badge-<?= $bc ?>"><?= ucfirst($e['status']) ?></span>
          </td>
          <td>
            <?php if ($e['status']==='pendente'): ?>
            <a href="<?= BASE_URL ?>/?page=exames.aprovar&id=<?= $e['id'] ?>"
               class="btn btn-success btn-xs"
               data-confirm="Aprovar exame de <?= h($e['nome_completo']) ?>? A faixa será atualizada.">✓ Aprovar</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

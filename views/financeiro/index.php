<?php $pageTitle = 'Mensalidades'; require_once __DIR__ . '/../layouts/header.php';
$mesAtual = $_GET['mes'] ?? date('Y-m');
?>

<!-- Filtros -->
<form method="GET" action="<?= BASE_URL ?>/" class="filter-bar">
  <input type="hidden" name="page" value="financeiro">
  <input type="text" name="busca" class="form-control" placeholder="🔍 Buscar aluno..."
         value="<?= h($_GET['busca'] ?? '') ?>" style="min-width:200px">
  <input type="month" name="mes" class="form-control" value="<?= h($mesAtual) ?>">
  <?php if (isAdmin()): ?>
  <select name="academia_id" class="form-control">
    <option value="">Todas as academias</option>
    <?php foreach ($academias as $ac): ?>
    <option value="<?= $ac['id'] ?>" <?= ($_GET['academia_id']??'')==$ac['id']?'selected':'' ?>><?= h($ac['nome']) ?></option>
    <?php endforeach; ?>
  </select>
  <?php endif; ?>
  <select name="status" class="form-control">
    <option value="">Todos os status</option>
    <option value="pago"     <?= ($_GET['status']??'')==='pago'?'selected':'' ?>>✅ Pago</option>
    <option value="pendente" <?= ($_GET['status']??'')==='pendente'?'selected':'' ?>>⏳ Pendente</option>
    <option value="atrasado" <?= ($_GET['status']??'')==='atrasado'?'selected':'' ?>>🔴 Atrasado</option>
    <option value="integral" <?= ($_GET['status']??'')==='integral'?'selected':'' ?>>🟢 Integral</option>
  </select>
  <button type="submit" class="btn btn-primary">Filtrar</button>
  <a href="<?= BASE_URL ?>/?page=financeiro" class="btn btn-outline">Limpar</a>
</form>

<!-- Resumo do mês -->
<?php
$totPago = 0;
foreach ($mensalidades as $_m) {
    if ($_m['status'] === 'pago') $totPago += $_m['valor'];
}
$totPendente = array_filter($mensalidades, function($_m) {
    return in_array($_m['status'], ['pendente','atrasado']);
});
?>
<div class="stats-grid" style="margin-bottom:16px">
  <div class="stat-card green">
    <div class="label">Recebido</div>
    <div class="value" style="font-size:22px"><?= formatMoeda($totPago) ?></div>
    <div class="sub"><?= count(array_filter($mensalidades,fn($m)=>$m['status']==='pago')) ?> pagamentos</div>
  </div>
  <div class="stat-card red">
    <div class="label">Em aberto</div>
    <div class="value"><?= count($totPendente) ?></div>
    <div class="sub">pendentes/atrasados</div>
  </div>
  <div class="stat-card blue">
    <div class="label">Total lançado</div>
    <div class="value"><?= count($mensalidades) ?></div>
    <div class="sub">registros</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span>💰</span>
    <h2>Mensalidades — <?= mesReferencia($mesAtual) ?></h2>
    <div class="d-flex gap-8 ms-auto">
      <a href="<?= BASE_URL ?>/?page=financeiro.lancar" class="btn btn-success btn-sm">+ Lançar</a>
      <?php if (isAdmin()): ?>
      <a href="<?= BASE_URL ?>/?page=financeiro.gerar" class="btn btn-warning btn-sm">⚙ Lote</a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (empty($mensalidades)): ?>
    <div class="empty-state">
      <div class="icon">💳</div>
      <h3>Nenhuma mensalidade encontrada</h3>
      <p>Ajuste os filtros ou <a href="<?= BASE_URL ?>/?page=financeiro.lancar">lance uma nova mensalidade</a>.</p>
    </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>T</th>
          <th>Aluno</th>
          <th>Academia</th>
          <th>Mês</th>
          <th>Valor</th>
          <th>Vencimento</th>
          <th>Status</th>
          <th>Forma Pgto</th>
          <th>Data Pgto</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($mensalidades as $m): ?>
        <?php
        $rowClass = '';
        if ($m['status']==='atrasado') $rowClass = 'style="background:#fff5f5"';
        elseif ($m['status']==='pago') $rowClass = 'style="background:#f0fdf4"';
        elseif ($m['status']==='integral') $rowClass = 'style="background:#f0f9ff"';
        ?>
        <tr <?= $rowClass ?>>
          <td>
            <?php $turno = $m['turno'] ?? ''; ?>
            <span class="turno-<?= strtolower($turno) ?>"><?= h($turno ?: '-') ?></span>
          </td>
          <td class="fw-600">
            <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= $m['aluno_id'] ?>"
               style="color:inherit;text-decoration:none"><?= h($m['nome_completo']) ?></a>
          </td>
          <td class="text-muted text-small"><?= h($m['academia_nome'] ?? '-') ?></td>
          <td><?= mesReferencia($m['mes_referencia']) ?></td>
          <td><?= formatMoeda($m['valor']) ?></td>
          <td><?= formatData($m['data_vencimento']) ?></td>
          <td>
            <?php
            $badges = ['pago'=>'success','pendente'=>'warning','atrasado'=>'danger','integral'=>'info'];
            $bc = $badges[$m['status']] ?? 'secondary';
            $icons = ['pago'=>'✅','pendente'=>'⏳','atrasado'=>'🔴','integral'=>'🟢'];
            echo '<span class="badge badge-'.$bc.'">'.$icons[$m['status']].' '.ucfirst($m['status']).'</span>';
            ?>
          </td>
          <td><?= h($m['forma_pagamento'] ?? '—') ?></td>
          <td><?= formatData($m['data_pagamento']) ?></td>
          <td>
            <div class="d-flex gap-8">
              <?php if ($m['status'] !== 'pago' && $m['status'] !== 'integral'): ?>
              <a href="<?= BASE_URL ?>/?page=financeiro.pagar&id=<?= $m['id'] ?>"
                 class="btn btn-success btn-xs">Pagar</a>
              <?php endif; ?>
              <?php if (isAdmin()): ?>
              <a href="<?= BASE_URL ?>/?page=financeiro.excluir&id=<?= $m['id'] ?>"
                 class="btn btn-danger btn-xs"
                 data-confirm="Excluir este lançamento?">🗑</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

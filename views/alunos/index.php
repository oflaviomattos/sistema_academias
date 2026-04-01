<?php $pageTitle = 'Alunos'; require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Filtros -->
<form method="GET" action="<?= BASE_URL ?>/" class="filter-bar" style="margin-bottom:16px">
  <input type="hidden" name="page" value="alunos">
  <input type="text" name="busca" class="form-control" placeholder="🔍 Buscar aluno ou responsável..."
         value="<?= h($_GET['busca'] ?? '') ?>" style="min-width:220px">
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
    <option value="ativo"   <?= ($_GET['status']??'')==='ativo'?'selected':'' ?>>Ativo</option>
    <option value="inativo" <?= ($_GET['status']??'')==='inativo'?'selected':'' ?>>Inativo</option>
  </select>
  <select name="faixa" class="form-control">
    <option value="">Todas as faixas</option>
    <?php foreach ($faixas as $f): ?>
    <option value="<?= h($f['nome']) ?>" <?= ($_GET['faixa']??'')===$f['nome']?'selected':'' ?>><?= h($f['nome']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="serie" class="form-control">
    <option value="">Todas as séries</option>
    <optgroup label="Infantil (2 a 5 anos)">
      <?php for ($i=2;$i<=5;$i++): ?>
      <option value="Infantil <?= $i ?>" <?= ($_GET['serie']??'')=='Infantil '.$i?'selected':'' ?>>Infantil <?= $i ?> ano<?= $i>1?'s':'' ?></option>
      <?php endfor; ?>
    </optgroup>
    <optgroup label="Fundamental I (1 ao 5 ano)">
      <?php for ($i=1;$i<=5;$i++): ?>
      <option value="Fund I <?= $i ?>" <?= ($_GET['serie']??'')=='Fund I '.$i?'selected':'' ?>>Fundamental I - <?= $i ?>º ano</option>
      <?php endfor; ?>
    </optgroup>
    <optgroup label="Fundamental II (6 ao 9 ano)">
      <?php for ($i=6;$i<=9;$i++): ?>
      <option value="Fund II <?= $i ?>" <?= ($_GET['serie']??'')=='Fund II '.$i?'selected':'' ?>>Fundamental II - <?= $i ?>º ano</option>
      <?php endfor; ?>
    </optgroup>
  </select>
  <button type="submit" class="btn btn-primary">Filtrar</button>
  <a href="<?= BASE_URL ?>/?page=alunos" class="btn btn-outline">Limpar</a>
</form>

<div class="card">
  <div class="card-header">
    <span>👥</span>
    <h2>Alunos <span class="badge badge-secondary" style="font-size:12px"><?= count($alunos) ?></span></h2>
    <div class="d-flex gap-8 ms-auto">
      <a href="<?= BASE_URL ?>/?page=alunos.criar" class="btn btn-primary btn-sm">+ Novo Aluno</a>
      <a href="<?= BASE_URL ?>/?page=importacao" class="btn btn-outline btn-sm">📥 Importar CSV</a>
    </div>
  </div>

  <?php if (empty($alunos)): ?>
    <div class="empty-state">
      <div class="icon">👤</div>
      <h3>Nenhum aluno encontrado</h3>
      <p>Ajuste os filtros ou <a href="<?= BASE_URL ?>/?page=alunos.criar">cadastre um novo aluno</a>.</p>
    </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>T</th>
          <th>Contrato</th>
          <th>Aluno</th>
          <th>#</th>
          <th>Faixa</th>
          <th>Tam.</th>
          <th>Responsável</th>
          <th>Contato</th>
          <th>Academia</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($alunos as $a): ?>
        <tr>
          <td><span class="turno-<?= strtolower($a['turno']) ?>"><?= h($a['turno']) ?></span></td>
          <td style="text-align:center">
            <?php if ($a['contrato_ok']): ?>
              <span class="contrato-ok" title="Contrato ok">✓</span>
            <?php else: ?>
              <span class="contrato-no" title="Sem contrato">—</span>
            <?php endif; ?>
          </td>
          <td class="fw-600">
            <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= $a['id'] ?>" style="color:inherit;text-decoration:none">
              <?= h($a['nome_completo']) ?>
            </a>
          </td>
          <td><?= $a['serie_nivel'] ?? '-' ?></td>
          <td><span class="faixa-badge faixa-<?= strtolower(explode('/',$a['faixa'])[0]) ?>"><?= h($a['faixa']) ?></span></td>
          <td><?= h($a['tamanho'] ?? '-') ?></td>
          <td><?= h($a['responsavel_nome'] ?? '-') ?></td>
          <td><?= h($a['responsavel_telefone'] ?? '-') ?></td>
          <td><span class="text-muted text-small"><?= h($a['academia_nome'] ?? '-') ?></span></td>
          <td>
            <?php if ($a['status']==='ativo'): ?>
              <span class="badge badge-success">Ativo</span>
            <?php else: ?>
              <span class="badge badge-secondary">Inativo</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="d-flex gap-8">
              <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= $a['id'] ?>"
                 class="btn btn-outline btn-xs" title="Ver">👁</a>
              <a href="<?= BASE_URL ?>/?page=alunos.editar&id=<?= $a['id'] ?>"
                 class="btn btn-outline btn-xs" title="Editar">✏️</a>
              <?php if (isAdmin()): ?>
              <a href="<?= BASE_URL ?>/?page=alunos.excluir&id=<?= $a['id'] ?>"
                 class="btn btn-danger btn-xs"
                 data-confirm="Excluir o aluno <?= h($a['nome_completo']) ?>? Esta ação não pode ser desfeita."
                 title="Excluir">🗑</a>
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

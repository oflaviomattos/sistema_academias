<?php
$editando  = !empty($aluno['id']);
$pageTitle = $editando ? 'Editar Aluno' : 'Novo Aluno';
require_once __DIR__ . '/../layouts/header.php';
$a = $aluno; // atalho
?>

<div class="card" style="max-width:820px">
  <div class="card-header">
    <span><?= $editando ? '✏️' : '➕' ?></span>
    <h2><?= $pageTitle ?></h2>
    <a href="<?= BASE_URL ?>/?page=alunos" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <form method="POST"
          action="<?= BASE_URL ?>/?page=<?= $editando ? 'alunos.atualizar' : 'alunos.salvar' ?>">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
      <?php endif; ?>

      <!-- LINHA 1: Nome + Status -->
      <div class="form-row">
        <div class="form-group" style="flex:2">
          <label>Nome Completo *</label>
          <input type="text" name="nome_completo" class="form-control" required
                 value="<?= h($a['nome_completo'] ?? '') ?>" placeholder="Nome completo do aluno">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="ativo"   <?= ($a['status']??'ativo')==='ativo'?'selected':'' ?>>Ativo</option>
            <option value="inativo" <?= ($a['status']??'')==='inativo'?'selected':'' ?>>Inativo</option>
          </select>
        </div>
      </div>

      <!-- LINHA 2: Nasc + Turno + Contrato -->
      <div class="form-row">
        <div class="form-group">
          <label>Data de Nascimento</label>
          <input type="date" name="data_nascimento" class="form-control"
                 value="<?= h($a['data_nascimento'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Turno</label>
          <select name="turno" class="form-control">
            <?php foreach (TURNOS as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($a['turno']??'M')===$k?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Data de Entrada *</label>
          <input type="date" name="data_entrada" class="form-control" required
                 value="<?= h($a['data_entrada'] ?? date('Y-m-d')) ?>">
        </div>
      </div>

      <!-- LINHA 3: Série + Faixa + Tamanho + Contrato OK -->
      <div class="form-row">
        <div class="form-group">
          <label>Série / Nível</label>
          <select name="serie_nivel" class="form-control">
            <option value="">—</option>
            <?php for ($i=1;$i<=6;$i++): ?>
            <option value="<?= $i ?>" <?= ($a['serie_nivel']??'')==$i?'selected':'' ?>>Série <?= $i ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Faixa</label>
          <select name="faixa" class="form-control">
            <?php foreach (FAIXAS as $f): ?>
            <option value="<?= $f ?>" <?= ($a['faixa']??'branca')===$f?'selected':'' ?>><?= ucfirst($f) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tamanho (kimono)</label>
          <input type="text" name="tamanho" class="form-control"
                 value="<?= h($a['tamanho'] ?? '') ?>" placeholder="ex: M2, A1">
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px">
          <label class="form-check">
            <input type="checkbox" name="contrato_ok" value="1"
                   <?= !empty($a['contrato_ok'])?'checked':'' ?>>
            Contrato assinado
          </label>
        </div>
      </div>

      <!-- LINHA 4: Academia + Responsável -->
      <div class="form-row">
        <?php if (isAdmin()): ?>
        <div class="form-group">
          <label>Academia *</label>
          <select name="academia_id" class="form-control" required>
            <option value="">Selecione...</option>
            <?php foreach ($academias as $ac): ?>
            <option value="<?= $ac['id'] ?>"
              <?= ($a['academia_id']??'')==$ac['id']?'selected':'' ?>>
              <?= h($ac['nome']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php else: ?>
          <input type="hidden" name="academia_id" value="<?= (int)$_SESSION['academia_id'] ?>">
        <?php endif; ?>

        <div class="form-group">
          <label>Responsável
            <a href="<?= BASE_URL ?>/?page=responsaveis.criar" target="_blank"
               style="font-size:11px;margin-left:8px;color:var(--primary)">+ Novo</a>
          </label>
          <select name="responsavel_id" class="form-control">
            <option value="">Sem responsável</option>
            <?php foreach ($responsaveis as $r): ?>
            <option value="<?= $r['id'] ?>"
              <?= ($a['responsavel_id']??'')==$r['id']?'selected':'' ?>>
              <?= h($r['nome']) ?> — <?= h($r['telefone']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Observações -->
      <div class="form-group">
        <label>Observações</label>
        <textarea name="observacoes" class="form-control" rows="2"
                  placeholder="Informações adicionais..."><?= h($a['observacoes'] ?? '') ?></textarea>
      </div>

      <div class="d-flex gap-8">
        <button type="submit" class="btn btn-primary">
          <?= $editando ? '💾 Salvar Alterações' : '✅ Cadastrar Aluno' ?>
        </button>
        <a href="<?= BASE_URL ?>/?page=alunos" class="btn btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

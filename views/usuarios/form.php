<?php
$editando  = !empty($usuario['id']);
$pageTitle = $editando ? 'Editar Usuario' : 'Novo Usuario';
require_once __DIR__ . '/../layouts/header.php';
$u = $usuario;
?>

<div class="card" style="max-width:560px">
  <div class="card-header">
    <span><?= $editando ? '✏️' : '➕' ?></span>
    <h2><?= $pageTitle ?></h2>
    <a href="<?= BASE_URL ?>/?page=usuarios" class="btn btn-outline btn-sm ms-auto">← Voltar</a>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?page=<?= $editando ? 'usuarios.atualizar' : 'usuarios.salvar' ?>">
      <?php if ($editando): ?><input type="hidden" name="id" value="<?= $u['id'] ?>"><?php endif; ?>

      <div class="form-group">
        <label>Nome completo *</label>
        <input type="text" name="nome" class="form-control" required
               value="<?= h($u['nome'] ?? '') ?>" placeholder="Nome do usuario">
      </div>

      <div class="form-group">
        <label>E-mail *</label>
        <input type="email" name="email" class="form-control" required
               value="<?= h($u['email'] ?? '') ?>" placeholder="email@exemplo.com">
      </div>

      <?php if ($editando): ?>
      <!-- Senha opcional na edicao -->
      <div class="form-group">
        <label>Nova senha <span style="font-size:12px;color:#94a3b8">(deixe em branco para manter a atual)</span></label>
        <input type="password" name="nova_senha" class="form-control"
               placeholder="Minimo 6 caracteres" autocomplete="new-password">
      </div>
      <?php else: ?>
      <div class="form-group">
        <label>Senha *</label>
        <input type="password" name="senha" class="form-control" required
               placeholder="Minimo 6 caracteres" autocomplete="new-password">
      </div>
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group">
          <label>Perfil *</label>
          <select name="perfil" class="form-control" required onchange="toggleAcademia(this.value)">
            <?php foreach (PERFIS as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($u['perfil'] ?? 'usuario') === $key ? 'selected' : '' ?>>
              <?= $label ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" id="campo-academia">
          <label>Academia vinculada</label>
          <select name="academia_id" class="form-control">
            <option value="">— Todas (admin global) —</option>
            <?php foreach ($academias as $ac): ?>
            <option value="<?= $ac['id'] ?>"
              <?= ($u['academia_id'] ?? '') == $ac['id'] ? 'selected' : '' ?>>
              <?= h($ac['nome']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <span style="font-size:11.5px;color:#94a3b8">Obrigatorio para perfil Usuario</span>
        </div>
      </div>

      <?php if ($editando): ?>
      <div class="form-group">
        <label>Status</label>
        <select name="ativo" class="form-control">
          <option value="1" <?= ($u['ativo'] ?? 1) ? 'selected' : '' ?>>Ativo</option>
          <option value="0" <?= !($u['ativo'] ?? 1) ? 'selected' : '' ?>>Inativo</option>
        </select>
      </div>
      <?php endif; ?>

      <div class="d-flex gap-8" style="margin-top:8px">
        <button type="submit" class="btn btn-primary">
          <?= $editando ? '💾 Salvar Alteracoes' : '✅ Criar Usuario' ?>
        </button>
        <a href="<?= BASE_URL ?>/?page=usuarios" class="btn btn-outline">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script>
function toggleAcademia(perfil) {
    var campo = document.getElementById('campo-academia');
    // Admin nao precisa de academia obrigatoria
    campo.style.opacity = perfil === 'admin' ? '0.5' : '1';
}
// Inicializa
toggleAcademia(document.querySelector('[name=perfil]').value);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

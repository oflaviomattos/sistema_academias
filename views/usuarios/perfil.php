<?php $pageTitle = 'Meu Perfil'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">

  <!-- CARD DO USUARIO -->
  <div class="card">
    <div class="card-body" style="text-align:center;padding:28px 20px">
      <div style="width:72px;height:72px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px">
        <?php
          $icones = ['admin'=>'🔴','financeiro'=>'💰','usuario'=>'👤'];
          echo isset($icones[$usuario['perfil']]) ? $icones[$usuario['perfil']] : '👤';
        ?>
      </div>
      <div style="font-size:18px;font-weight:700"><?= h($usuario['nome']) ?></div>
      <div style="font-size:13px;color:#64748b;margin-top:4px"><?= h($usuario['email']) ?></div>
      <div style="margin-top:12px">
        <?php
          $badges = ['admin'=>'badge-danger','financeiro'=>'badge-success','usuario'=>'badge-info'];
          $bc = isset($badges[$usuario['perfil']]) ? $badges[$usuario['perfil']] : 'badge-secondary';
          $labels = isset(PERFIS[$usuario['perfil']]) ? PERFIS[$usuario['perfil']] : $usuario['perfil'];
        ?>
        <span class="badge <?= $bc ?>"><?= $labels ?></span>
      </div>
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9">
        <a href="<?= APP_URL ?>/index.php?page=logout"
           style="color:#dc2626;font-size:13.5px;text-decoration:none;font-weight:500">
          🚪 Sair do sistema
        </a>
      </div>
    </div>
  </div>

  <!-- ALTERAR SENHA -->
  <div class="card">
    <div class="card-header"><span>🔑</span><h2>Alterar senha</h2></div>
    <div class="card-body">
      <form method="POST" action="<?= BASE_URL ?>/?page=perfil.senha">
        <div class="form-group">
          <label>Senha atual *</label>
          <input type="password" name="senha_atual" class="form-control" required
                 placeholder="Digite sua senha atual">
        </div>
        <div class="form-group">
          <label>Nova senha *</label>
          <input type="password" name="nova_senha" id="nova_senha" class="form-control" required
                 placeholder="Minimo 6 caracteres">
        </div>
        <div class="form-group">
          <label>Confirmar nova senha *</label>
          <input type="password" name="confirma" id="confirma" class="form-control" required
                 placeholder="Repita a nova senha">
          <span id="msg-confirma" style="font-size:12px;margin-top:4px;display:block"></span>
        </div>
        <button type="submit" class="btn btn-primary">🔑 Alterar Senha</button>
      </form>
    </div>
  </div>

</div>

<script>
// Valida confirmacao de senha em tempo real
document.getElementById('confirma').addEventListener('input', function() {
    var nova  = document.getElementById('nova_senha').value;
    var conf  = this.value;
    var msg   = document.getElementById('msg-confirma');
    if (conf === '') { msg.textContent = ''; return; }
    if (nova === conf) {
        msg.textContent = '✓ Senhas coincidem';
        msg.style.color = '#16a34a';
    } else {
        msg.textContent = '✕ Senhas nao coincidem';
        msg.style.color = '#dc2626';
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

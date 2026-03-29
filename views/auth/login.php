<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div style="font-size:36px;margin-bottom:12px">🥋</div>
    <h1><?= APP_NAME ?></h1>
    <p>Faça login para acessar o sistema</p>

    <?php if ($erro): ?>
    <div class="alert alert-danger"><?= h($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/?page=login">
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" class="form-control"
               value="<?= h($_POST['email'] ?? '') ?>"
               placeholder="seu@email.com" required autofocus>
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" class="form-control"
               placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
        Entrar
      </button>
    </form>

    <p style="margin-top:20px;font-size:12px;color:#94a3b8;text-align:center">
      Esqueceu a senha? Fale com o administrador.
    </p>
  </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= h($pageTitle ?? 'Sistema') ?> — <?= APP_NAME ?></title>

  <!-- PWA: Manifest -->
  <link rel="manifest" href="<?= BASE_URL ?>/public/manifest.json">

  <!-- PWA: Tema e cores -->
  <meta name="theme-color" content="#1a56db">
  <meta name="background-color" content="#1e2a3a">

  <!-- PWA: iOS (Safari) -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Academia">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/public/icons/apple-touch-icon.png">
  <link rel="apple-touch-icon" sizes="152x152" href="<?= BASE_URL ?>/public/icons/icon-152.png">
  <link rel="apple-touch-icon" sizes="192x192" href="<?= BASE_URL ?>/public/icons/icon-192.png">

  <!-- Splash screens iOS -->
  <meta name="apple-mobile-web-app-status-bar-style" content="default">

  <!-- PWA: Android / geral -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="application-name" content="Academia">
  <meta name="msapplication-TileColor" content="#1e2a3a">
  <meta name="msapplication-TileImage" content="<?= BASE_URL ?>/public/icons/icon-144.png">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/public/icons/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/public/icons/favicon-16.png">
  <link rel="shortcut icon" href="<?= BASE_URL ?>/public/favicon.ico">

  <!-- Fontes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">

  <!-- PWA: registra o Service Worker -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('<?= BASE_URL ?>/public/sw.js', {
          scope: '<?= BASE_URL ?>/'
        }).then(function(reg) {
          console.log('SW registrado:', reg.scope);
        }).catch(function(err) {
          console.log('SW erro:', err);
        });
      });
    }
  </script>

</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <h1>🥋 <?= APP_NAME ?></h1>
    <p><?= h($_SESSION['usuario_nome'] ?? '') ?> · <?= isAdmin() ? 'Admin' : 'Usuário' ?></p>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Principal</div>
    <a href="<?= BASE_URL ?>/?page=dashboard" class="nav-item <?= ($page==='dashboard')?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="nav-group-label">Cadastros</div>
    <a href="<?= BASE_URL ?>/?page=alunos" class="nav-item <?= (strpos($page,'alunos')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Alunos
    </a>
    <a href="<?= BASE_URL ?>/?page=responsaveis" class="nav-item <?= (strpos($page,'responsaveis')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Responsaveis
    </a>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/?page=academias" class="nav-item <?= (strpos($page,'academias')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Academias
    </a>
    <?php endif; ?>

    <div class="nav-group-label">Financeiro</div>
    <a href="<?= BASE_URL ?>/?page=financeiro" class="nav-item <?= (strpos($page,'financeiro')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Mensalidades
    </a>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/?page=financeiro.gerar" class="nav-item <?= ($page==='financeiro.gerar')?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Gerar em Lote
    </a>
    <?php endif; ?>

    <div class="nav-group-label">Eventos</div>
    <a href="<?= BASE_URL ?>/?page=exames" class="nav-item <?= (strpos($page,'exames')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Exames de Faixa
    </a>
    <a href="<?= BASE_URL ?>/?page=campeonatos" class="nav-item <?= (strpos($page,'campeonatos')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
      Campeonatos
    </a>

    <?php if (isAdmin()): ?>
    <div class="nav-group-label">Ferramentas</div>
    <a href="<?= BASE_URL ?>/?page=faixas" class="nav-item <?= (strpos($page,'faixas')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      Faixas
    </a>
    <a href="<?= BASE_URL ?>/?page=configuracoes" class="nav-item <?= (strpos($page,'configuracoes')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      Configuracoes
    </a>
    <a href="<?= BASE_URL ?>/?page=usuarios" class="nav-item <?= (strpos($page,'usuarios')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Usuarios
    </a>
    <a href="<?= BASE_URL ?>/?page=importacao" class="nav-item <?= (strpos($page,'importacao')===0)?'active':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Importar CSV
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <!-- Info do usuario logado -->
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.07)">
      <div style="width:34px;height:34px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">
        <?php
          $ic = ['admin'=>'🔴','financeiro'=>'💰','usuario'=>'👤'];
          $perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : 'usuario';
          echo isset($ic[$perfil]) ? $ic[$perfil] : '👤';
        ?>
      </div>
      <div style="overflow:hidden">
        <div style="font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          <?= h($_SESSION['usuario_nome'] ?? '') ?>
        </div>
        <div style="font-size:11px;color:#6b7f96">
          <?= isset(PERFIS[$perfil]) ? PERFIS[$perfil] : $perfil ?>
        </div>
      </div>
    </div>
    <!-- Acoes -->
    <div style="display:flex;gap:8px">
      <a href="<?= APP_URL ?>/index.php?page=perfil"
         style="flex:1;text-align:center;padding:7px;background:rgba(255,255,255,.07);border-radius:6px;font-size:12px;color:#a8b8cc;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='rgba(255,255,255,.13)'"
         onmouseout="this.style.background='rgba(255,255,255,.07)'">
        ⚙️ Perfil
      </a>
      <a href="<?= APP_URL ?>/index.php?page=logout"
         style="flex:1;text-align:center;padding:7px;background:rgba(220,38,38,.15);border-radius:6px;font-size:12px;color:#fca5a5;text-decoration:none;transition:background .15s"
         onmouseover="this.style.background='rgba(220,38,38,.25)'"
         onmouseout="this.style.background='rgba(220,38,38,.15)'">
        🚪 Sair
      </a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main-wrapper">
  <div class="topbar">
    <button id="menu-btn" aria-label="Menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="3" y1="6"  x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
    <span class="topbar-title"><?= h($pageTitle ?? '') ?></span>
    <button id="btn-ocultar-valores" onclick="toggleValores()" title="Ocultar/mostrar valores"
            style="background:none;border:1px solid #d1d5db;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:16px;margin-right:8px">
      👁
    </button>
    <span class="topbar-user">
      <?php if (!isAdmin() && !empty($_SESSION['academia_id'])): ?>
        <?php
          $acStmt = getDB()->prepare("SELECT nome FROM academias WHERE id=:id");
          $acStmt->execute(['id'=>$_SESSION['academia_id']]);
          $acNome = $acStmt->fetchColumn();
        ?>
        <span class="badge badge-info"><?= h($acNome ?: '') ?></span>
      <?php endif; ?>
    </span>
  </div>

  <main class="content">
    <?php $flash = flashGet(); if ($flash): ?>
    <div class="alert alert-<?= h($flash['tipo']) ?>">
      <?= $flash['tipo'] === 'success' ? '✓' : ($flash['tipo'] === 'danger' ? '✕' : '⚠') ?>
      <?= h($flash['msg']) ?>
    </div>
    <?php endif; ?>

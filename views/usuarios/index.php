<?php $pageTitle = 'Usuarios do Sistema'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
  <div class="card-header">
    <span>👤</span>
    <h2>Usuarios <span class="badge badge-secondary"><?= count($usuarios) ?></span></h2>
    <a href="<?= BASE_URL ?>/?page=usuarios.criar" class="btn btn-primary btn-sm ms-auto">+ Novo Usuario</a>
  </div>

  <?php if (empty($usuarios)): ?>
    <div class="empty-state"><div class="icon">👤</div><h3>Nenhum usuario cadastrado</h3></div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Perfil</th>
          <th>Academia</th>
          <th>Status</th>
          <th>Acoes</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($usuarios as $u): ?>
        <?php
          $badgePerfil = [
              'admin'      => 'badge-danger',
              'financeiro' => 'badge-success',
              'usuario'    => 'badge-info',
          ];
          $iconePerfil = [
              'admin'      => '🔴',
              'financeiro' => '💰',
              'usuario'    => '👤',
          ];
          $bc = isset($badgePerfil[$u['perfil']]) ? $badgePerfil[$u['perfil']] : 'badge-secondary';
          $ic = isset($iconePerfil[$u['perfil']]) ? $iconePerfil[$u['perfil']] : '👤';
        ?>
        <tr <?= !$u['ativo'] ? 'style="opacity:.5"' : '' ?>>
          <td>
            <div style="font-weight:600"><?= h($u['nome']) ?></div>
            <?php if ((int)$u['id'] === (int)$_SESSION['usuario_id']): ?>
              <span style="font-size:11px;color:#16a34a">▶ voce</span>
            <?php endif; ?>
          </td>
          <td><?= h($u['email']) ?></td>
          <td>
            <span class="badge <?= $bc ?>">
              <?= $ic ?> <?= h(isset(PERFIS[$u['perfil']]) ? PERFIS[$u['perfil']] : $u['perfil']) ?>
            </span>
          </td>
          <td><?= h($u['academia_nome'] ?? '— todas —') ?></td>
          <td>
            <?php if ($u['ativo']): ?>
              <span class="badge badge-success">Ativo</span>
            <?php else: ?>
              <span class="badge badge-secondary">Inativo</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="d-flex gap-8">
              <a href="<?= BASE_URL ?>/?page=usuarios.editar&id=<?= $u['id'] ?>"
                 class="btn btn-outline btn-xs">✏️ Editar</a>
              <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
              <a href="<?= BASE_URL ?>/?page=usuarios.excluir&id=<?= $u['id'] ?>"
                 class="btn btn-danger btn-xs"
                 data-confirm="Excluir o usuario <?= h($u['nome']) ?>?">🗑</a>
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

<!-- Legenda de perfis -->
<div class="card" style="margin-top:20px">
  <div class="card-header"><span>ℹ️</span><h2>Permissoes por perfil</h2></div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px">

      <div style="border:1px solid #fecaca;border-radius:8px;padding:16px;background:#fff5f5">
        <div style="font-weight:700;margin-bottom:8px;color:#991b1b">🔴 Administrador</div>
        <ul style="font-size:13px;color:#374151;padding-left:18px;line-height:2">
          <li>Acesso total ao sistema</li>
          <li>Cria e gerencia usuarios</li>
          <li>Gerencia academias</li>
          <li>Importa CSV</li>
          <li>Gera mensalidades em lote</li>
        </ul>
      </div>

      <div style="border:1px solid #bbf7d0;border-radius:8px;padding:16px;background:#f0fdf4">
        <div style="font-weight:700;margin-bottom:8px;color:#166534">💰 Financeiro</div>
        <ul style="font-size:13px;color:#374151;padding-left:18px;line-height:2">
          <li>Dashboard completo</li>
          <li>Gerencia mensalidades</li>
          <li>Visualiza e edita alunos</li>
          <li>Exames e campeonatos</li>
          <li><em style="color:#64748b">Sem acesso a usuarios/academias/importacao</em></li>
        </ul>
      </div>

      <div style="border:1px solid #bfdbfe;border-radius:8px;padding:16px;background:#eff6ff">
        <div style="font-weight:700;margin-bottom:8px;color:#1d4ed8">👤 Usuario</div>
        <ul style="font-size:13px;color:#374151;padding-left:18px;line-height:2">
          <li>Dashboard basico</li>
          <li>Visualiza alunos</li>
          <li>Exames e campeonatos</li>
          <li><em style="color:#64748b">Sem acesso ao financeiro</em></li>
          <li><em style="color:#64748b">Restrito a uma academia</em></li>
        </ul>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

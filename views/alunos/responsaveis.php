<?php $pageTitle = 'Responsaveis'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
  <div class="card-header">
    <span>👨‍👩‍👧</span>
    <h2>Responsaveis <span class="badge badge-secondary"><?= count($responsaveis) ?></span></h2>
    <div class="d-flex gap-8 ms-auto">
      <form method="GET" action="<?= BASE_URL ?>/" class="d-flex gap-8">
        <input type="hidden" name="page" value="responsaveis">
        <input type="text" name="busca" class="form-control" placeholder="🔍 Buscar nome ou telefone..."
               value="<?= h($_GET['busca'] ?? '') ?>" style="width:240px">
        <button type="submit" class="btn btn-outline btn-sm">Buscar</button>
        <?php if (!empty($_GET['busca'])): ?>
        <a href="<?= BASE_URL ?>/?page=responsaveis" class="btn btn-outline btn-sm">✕</a>
        <?php endif; ?>
      </form>
      <a href="<?= BASE_URL ?>/?page=responsaveis.criar" class="btn btn-primary btn-sm">+ Novo</a>
    </div>
  </div>

  <?php if (empty($responsaveis)): ?>
    <div class="empty-state">
      <div class="icon">👨‍👩‍👧</div>
      <h3>Nenhum responsavel cadastrado</h3>
      <p><a href="<?= BASE_URL ?>/?page=responsaveis.criar">Cadastrar o primeiro responsavel</a></p>
    </div>
  <?php else: ?>

  <!-- Contador resumo -->
  <?php
    $totalAlunos = 0;
    foreach ($alunosPorResponsavel as $lista) $totalAlunos += count($lista);
  ?>
  <div style="padding:12px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-size:13px;color:#64748b">
    <?= count($responsaveis) ?> responsaveis · <?= $totalAlunos ?> alunos vinculados
  </div>

  <div style="divide-y:1px solid #f1f5f9">
    <?php foreach ($responsaveis as $r):
      $alunos = $alunosPorResponsavel[(int)$r['id']] ?? [];
      $temAlunos = count($alunos) > 0;
    ?>
    <div class="responsavel-row" style="border-bottom:1px solid #f1f5f9">

      <!-- Linha principal do responsável -->
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;cursor:<?= $temAlunos ? 'pointer' : 'default' ?>"
           <?= $temAlunos ? 'onclick="toggleAlunos('.$r['id'].')" ' : '' ?>>

        <!-- Avatar -->
        <div style="width:40px;height:40px;background:#e0e7ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
          👤
        </div>

        <!-- Info principal -->
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:14px"><?= h($r['nome']) ?></div>
          <div style="display:flex;gap:16px;margin-top:3px;font-size:12.5px;color:#64748b;flex-wrap:wrap">
            <span>
              <a href="https://wa.me/55<?= preg_replace('/\D/', '', $r['telefone']) ?>"
                 target="_blank"
                 onclick="event.stopPropagation()"
                 style="color:#25D366;text-decoration:none">
                📱 <?= h($r['telefone']) ?>
              </a>
            </span>
            <?php if ($r['email']): ?>
            <span>✉️ <?= h($r['email']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Badge de alunos -->
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
          <?php if ($temAlunos): ?>
          <span style="background:#dbeafe;color:#1d4ed8;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600">
            <?= count($alunos) ?> <?= count($alunos) === 1 ? 'aluno' : 'alunos' ?>
          </span>
          <span id="arrow-<?= $r['id'] ?>" style="color:#94a3b8;font-size:12px;transition:transform .2s">▼</span>
          <?php else: ?>
          <span style="background:#f1f5f9;color:#94a3b8;padding:4px 12px;border-radius:20px;font-size:12px">
            sem alunos
          </span>
          <?php endif; ?>

          <!-- Ações -->
          <div class="d-flex gap-8" onclick="event.stopPropagation()">
            <a href="<?= BASE_URL ?>/?page=responsaveis.editar&id=<?= $r['id'] ?>"
               class="btn btn-outline btn-xs" title="Editar">✏️</a>
            <?php if (isAdmin() && !$temAlunos): ?>
            <a href="<?= BASE_URL ?>/?page=responsaveis.excluir&id=<?= $r['id'] ?>"
               class="btn btn-danger btn-xs"
               data-confirm="Excluir <?= h($r['nome']) ?>?" title="Excluir">🗑</a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Alunos expandíveis -->
      <?php if ($temAlunos): ?>
      <div id="alunos-<?= $r['id'] ?>" style="display:none;background:#f8fafc;border-top:1px solid #e2e8f0;padding:4px 20px 12px 72px">
        <div style="font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin:10px 0 8px">
          Alunos vinculados
        </div>
        <div style="display:flex;flex-direction:column;gap:6px">
          <?php foreach ($alunos as $a): ?>
          <div style="display:flex;align-items:center;gap:10px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px">

            <!-- Faixa colorida -->
            <span class="faixa-badge faixa-<?= strtolower(explode('/',$a['faixa'])[0]) ?>" style="flex-shrink:0">
              <?= h($a['faixa']) ?>
            </span>

            <!-- Nome e info -->
            <div style="flex:1;min-width:0">
              <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= $a['id'] ?>"
                 style="font-weight:600;font-size:13.5px;color:#1e293b;text-decoration:none">
                <?= h($a['nome_completo']) ?>
              </a>
              <div style="font-size:12px;color:#64748b;margin-top:1px">
                <?php
                $sn = $a['serie_nivel'] ?? '';
                if (strpos($sn, 'Infantil ') === 0) {
                    echo 'Infantil ' . substr($sn, 9) . ' ano' . (substr($sn, 9) > 1 ? 's' : '') . ' · ';
                } elseif (strpos($sn, 'Fund I ') === 0) {
                    echo 'Fund I ' . substr($sn, 7) . 'º · ';
                } elseif (strpos($sn, 'Fund II ') === 0) {
                    echo 'Fund II ' . substr($sn, 8) . 'º · ';
                } elseif ($sn) {
                    echo h($sn) . ' · ';
                }
                ?>
                <?= h($a['academia_nome'] ?? '') ?>
              </div>
            </div>

            <!-- Turno -->
            <?php if (!empty($a['turno'])): ?>
            <span class="turno-<?= strtolower($a['turno'] ?? 'M') ?>" style="flex-shrink:0">
              <?= h($a['turno']) ?>
            </span>
            <?php endif; ?>

            <!-- Status -->
            <?php if ($a['status'] === 'ativo'): ?>
              <span class="badge badge-success" style="flex-shrink:0">Ativo</span>
            <?php else: ?>
              <span class="badge badge-secondary" style="flex-shrink:0">Inativo</span>
            <?php endif; ?>

            <!-- Link ver ficha -->
            <a href="<?= BASE_URL ?>/?page=alunos.ver&id=<?= $a['id'] ?>"
               class="btn btn-outline btn-xs" style="flex-shrink:0" title="Ver ficha">👁</a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
var expandidos = {};

function toggleAlunos(id) {
    var div   = document.getElementById('alunos-' + id);
    var arrow = document.getElementById('arrow-' + id);
    if (!div) return;

    if (expandidos[id]) {
        div.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
        expandidos[id] = false;
    } else {
        div.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        expandidos[id] = true;
    }
}

// Expande automaticamente se só há um responsável com alunos
var rows = document.querySelectorAll('[id^="alunos-"]');
if (rows.length === 1) {
    var id = rows[0].id.replace('alunos-', '');
    toggleAlunos(parseInt(id));
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

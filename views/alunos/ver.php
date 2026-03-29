<?php $pageTitle = h($aluno['nome_completo']); require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">

  <!-- FICHA DO ALUNO -->
  <div class="card">
    <div class="card-header">
      <span>👤</span>
      <h2>Ficha do Aluno</h2>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

      <div style="text-align:center;padding:12px 0">
        <div style="font-size:48px">🥋</div>
        <div style="font-size:17px;font-weight:700;margin-top:8px"><?= h($aluno['nome_completo']) ?></div>
        <?php if ($aluno['status']==='ativo'): ?>
          <span class="badge badge-success">Ativo</span>
        <?php else: ?>
          <span class="badge badge-secondary">Inativo</span>
        <?php endif; ?>
      </div>

      <hr style="border:none;border-top:1px solid #f1f5f9">

      <div style="display:grid;gap:10px;font-size:13px">
        <div><span class="text-muted">Academia:</span><br>
          <strong><?= h($aluno['academia_nome'] ?? '-') ?></strong></div>
        <div><span class="text-muted">Turno:</span>
          <span class="turno-<?= strtolower($aluno['turno']) ?>" style="margin-left:6px"><?= TURNOS[$aluno['turno']] ?? $aluno['turno'] ?></span>
        </div>
        <div><span class="text-muted">Série:</span>
          <strong><?= $aluno['serie_nivel'] ? 'Série '.$aluno['serie_nivel'] : '-' ?></strong></div>
        <div><span class="text-muted">Faixa:</span>
          <span class="faixa-badge faixa-<?= strtolower(explode('/',$aluno['faixa'])[0]) ?>" style="margin-left:6px"><?= h($aluno['faixa']) ?></span>
        </div>
        <div><span class="text-muted">Tamanho:</span> <strong><?= h($aluno['tamanho'] ?? '-') ?></strong></div>
        <div><span class="text-muted">Nascimento:</span> <strong><?= formatData($aluno['data_nascimento']) ?></strong></div>
        <div><span class="text-muted">Entrada:</span> <strong><?= formatData($aluno['data_entrada']) ?></strong></div>
        <div><span class="text-muted">Contrato:</span>
          <?php if ($aluno['contrato_ok']): ?>
            <span class="contrato-ok">✓ OK</span>
          <?php else: ?>
            <span class="contrato-no">Pendente</span>
          <?php endif; ?>
        </div>
      </div>

      <hr style="border:none;border-top:1px solid #f1f5f9">

      <div style="font-size:13px">
        <div class="text-muted" style="margin-bottom:4px">Responsável</div>
        <strong><?= h($aluno['responsavel_nome'] ?? 'Não informado') ?></strong>
        <?php if ($aluno['responsavel_telefone']): ?>
          <br><a href="https://wa.me/55<?= preg_replace('/\D/','',$aluno['responsavel_telefone']) ?>"
                 target="_blank" style="color:#25D366;font-size:13px">
            📱 <?= h($aluno['responsavel_telefone']) ?>
          </a>
        <?php endif; ?>
      </div>

      <?php if ($aluno['observacoes']): ?>
      <hr style="border:none;border-top:1px solid #f1f5f9">
      <div style="font-size:13px">
        <div class="text-muted" style="margin-bottom:4px">Observações</div>
        <?= h($aluno['observacoes']) ?>
      </div>
      <?php endif; ?>

      <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
        <a href="<?= BASE_URL ?>/?page=alunos.editar&id=<?= $aluno['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
        <a href="<?= BASE_URL ?>/?page=financeiro.lancar&aluno_id=<?= $aluno['id'] ?>" class="btn btn-success btn-sm">+ Lançar Mensalidade</a>
        <a href="<?= BASE_URL ?>/?page=exames.criar&aluno_id=<?= $aluno['id'] ?>" class="btn btn-outline btn-sm">🎽 Agendar Exame</a>
      </div>
    </div>
  </div>

  <!-- MENSALIDADES DO ALUNO -->
  <div class="card">
    <div class="card-header">
      <span>💰</span>
      <h2>Histórico de Mensalidades</h2>
      <a href="<?= BASE_URL ?>/?page=financeiro.lancar&aluno_id=<?= $aluno['id'] ?>"
         class="btn btn-success btn-sm ms-auto">+ Lançar</a>
    </div>
    <?php if (empty($mensalidades)): ?>
      <div class="empty-state"><div class="icon">💳</div><h3>Nenhuma mensalidade registrada</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Mês</th><th>Valor</th><th>Vencimento</th><th>Status</th><th>Pagamento</th><th>Forma</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($mensalidades as $m): ?>
          <tr>
            <td class="fw-600"><?= mesReferencia($m['mes_referencia']) ?></td>
            <td><?= formatMoeda($m['valor']) ?></td>
            <td><?= formatData($m['data_vencimento']) ?></td>
            <td>
              <?php
              $sc = ['pago'=>'success','pendente'=>'warning','atrasado'=>'danger','integral'=>'info'];
              $sc = $sc[$m['status']] ?? 'secondary';
              ?>
              <span class="badge badge-<?= $sc ?>"><?= h(ucfirst($m['status'])) ?></span>
            </td>
            <td><?= formatData($m['data_pagamento']) ?></td>
            <td><?= h($m['forma_pagamento'] ?? '-') ?></td>
            <td>
              <?php if ($m['status'] !== 'pago' && $m['status'] !== 'integral'): ?>
                <a href="<?= BASE_URL ?>/?page=financeiro.pagar&id=<?= $m['id'] ?>"
                   class="btn btn-success btn-xs">Pagar</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['usuario_id'])) {
    http_response_code(401); echo json_encode([]); exit;
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');

$busca      = trim($_GET['q'] ?? '');
$academiaId = (isset($_SESSION['perfil']) && $_SESSION['perfil'] !== 'admin')
              ? (isset($_SESSION['academia_id']) ? (int)$_SESSION['academia_id'] : null)
              : null;

if (strlen($busca) < 1) { echo json_encode([]); exit; }

$sql = "SELECT a.id, a.nome_completo, a.faixa, a.serie_nivel, ac.nome AS academia_nome
        FROM alunos a
        LEFT JOIN academias ac ON ac.id = a.academia_id
        WHERE a.status='ativo' AND a.nome_completo LIKE :q";
$params = ['q' => $busca . '%'];
if ($academiaId) { $sql .= " AND a.academia_id=:acid"; $params['acid'] = $academiaId; }
$sql .= " ORDER BY a.nome_completo ASC LIMIT 15";

$stmt = getDB()->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());

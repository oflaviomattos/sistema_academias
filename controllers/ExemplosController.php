<?php
// ============================================================
// CONTROLLER: Download de CSVs de exemplo
// ============================================================

$tipo = $_GET['tipo'] ?? 'alunos';

if ($tipo === 'alunos') {
    $filename = 'exemplo_alunos.csv';
    $rows = [
        // Cabeçalho — exatamente como a planilha do cliente
        ['manha / t', 'contrato', 'aluno', '#', 'faixa', 'tamanho', 'responsavel', 'contato'],
        // Exemplos reais baseados na planilha vista
        ['M', 'ok', 'Joao Guilherme Santos',       '1', '',       '',   'Jose Carlos',      '79 99636-9883'],
        ['M', 'ok', 'Davi de Jesus Leal Barros',   '1', 'azul',   '',   'Carla Ticiane',    '79 99199-6486'],
        ['M', '',   'Rafael Monte Alegre',          '1', 'cinza',  '',   'Igor do Nascimento','79 98805-4786'],
        ['M', 'ok', 'Flavio Henrique Ferreira Costa','2', 'branca', '',   'Selminha Tia',     '79 99991-7325'],
        ['M', 'ok', 'Maria Isabel de Souza Costa',  '2', '',       '',   'Deyci',            '79 99106-8940'],
        ['M', 'ok', 'Julia Vieira Santos',          '2', 'cinza',  '',   'Adrielly',         '79 98157-3007'],
        ['M', 'ok', 'Ana Beatriz Meneses Lopes',   '2', 'cinza',  '',   'Tatiana Meneses',  '79 99990-6700'],
        ['M', 'ok', 'Julia Dantas Nobrega',        '2', 'cinza',  '',   'Edyliane',         '79 98847-9540'],
        ['M', 'ok', 'Laiz Trindade de Lucena',     '2', 'Cinza',  '',   'Zelia',            '79 98103-1477'],
        ['M', 'ok', 'Talita Silva Gomes',          '2', 'cinza',  '',   'Meyre Jane',       '79 99632-0505'],
        ['M', 'ok', 'Ana Julia Almeida dos Anjos', '2', 'cinza',  '',   'Deise Alves',      '79 99953-9360'],
        ['T', 'ok', 'Lucas de Jesus Leal Barros',  '2', 'Azul',   '',   'Carla Ticiane',    '79 99199-6486'],
        ['M', '',   'Miguel Luduvice Nogueira',    '2', 'branca', '',   'Yasmin Maria',     '79 98800-9830'],
        ['M', 'ok', 'Bernado Fontes Lisboa',       '3', 'branca/cinza','','Michelle',       '79 99658-128'],
        ['M', 'ok', 'Eduardo Jose Gois Melo de Alcantara','4','cinza','','Diego Alcantara','79 99249-2711'],
        ['M', '',   'Gustavo Rodrigues Oliveira',  '4', 'cinza',  '',   'Karina Rodrigues', '79 99929-5872'],
        ['M', 'ok', 'Emanuel Lima Matias',         '5', 'Amarela','',   'Priscila Kity',    '79 99171-8920'],
        ['M', 'ok', 'Theo Vieira Santos',          '5', 'cinza',  '',   'Adrielly',         '79 98157-3007'],
        ['M', 'ok', 'Fernando Correia Aquino',     '5', 'azul/amarela','','Carlos',        '79 99140-1000'],
        ['M', 'ok', 'Murilo Silva de Souza',       '5', 'branca', '',   'Alessandra',       '79 99289-7582'],
    ];

} elseif ($tipo === 'mensalidades') {
    $filename = 'exemplo_mensalidades.csv';
    $rows = [
        // Cabeçalho com meses
        ['aluno', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        // Exemplos com os formatos reais da planilha
        ['Joao Guilherme Santos',        'Pix 02/01', 'Pix 02/02', 'Pix 02/03', 'Pendente', '', '', '', '', '', '', '', ''],
        ['Davi de Jesus Leal Barros',    'Pix 30/01', 'Pix 27/02', 'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Rafael Monte Alegre',          'Pix 28/01', 'Pix 05/02', 'Pix 04/03', '',          '', '', '', '', '', '', '', ''],
        ['Flavio Henrique Ferreira Costa','Pix 07/01','Pix 06/03', 'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Maria Isabel de Souza Costa',  '-',         'Pix 10/03', 'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Julia Vieira Santos',          'integral',  'integral',  'integral',  '',          '', '', '', '', '', '', '', ''],
        ['Ana Beatriz Meneses Lopes',    'Pix 18/02', 'ATRASADO',  'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Julia Dantas Nobrega',         'Pix 26/02', 'ATRASADO',  'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Talita Silva Gomes',           'Pix 29/01', 'Pix 02/03', 'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Ana Julia Almeida dos Anjos',  'Pix 10/02', 'ATRASADO',  'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Lucas de Jesus Leal Barros',   'Pix 30/01', 'Pix 27/02', 'Pendente',  '',          '', '', '', '', '', '', '', ''],
        ['Fernando Correia Aquino',      'Quitado',   'Quitado',   'Quitado',   '',          '', '', '', '', '', '', '', ''],
    ];
} else {
    flashSet('danger', 'Tipo de exemplo invalido.');
    redirect('importacao');
}

// Envia o arquivo CSV para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

// BOM para Excel abrir corretamente com acentos
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');
foreach ($rows as $row) {
    fputcsv($output, $row, ',');
}
fclose($output);
exit;

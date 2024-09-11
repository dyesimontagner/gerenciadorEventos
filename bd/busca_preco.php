<?php
include('conexao.php');

if (isset($_GET['id_evento'])) {
    $id_evento = $_GET['id_evento'];

    // Consulta para obter o preço do evento
    $sql = "SELECT i.preco, e.nome as nome_evento, e.LKORGANIZADOR, u.NOME as nome_org
            FROM ingressos as i
            inner join evento as e on e.id_evento = i.lkevento 
			inner JOIN organizador AS o on e.LKORGANIZADOR = o.ID_ORGANIZADOR
			INNER JOIN usuario AS u ON u.ID_USUARIO = o.ID_ORGANIZADOR
            WHERE lkevento = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id_evento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['sucesso' => true, 'preco' => $row['preco'], 'nome_evento' => $row['nome_evento'],  'nome_org' => $row['nome_org']  ]);
    } else {
        echo json_encode(['sucesso' => false]);
    }

    $stmt->close();
    $conexao->close();
} else {
    echo json_encode(['sucesso' => false]);
}

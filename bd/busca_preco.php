<?php
include('conexao.php');

if (isset($_GET['id_evento'])) {
    $id_evento = $_GET['id_evento'];

    // Consulta para obter o preço do evento
    $sql = "SELECT preco FROM eventos WHERE id_evento = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id_evento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['sucesso' => true, 'preco' => $row['preco']]);
    } else {
        echo json_encode(['sucesso' => false]);
    }

    $stmt->close();
    $conexao->close();
} else {
    echo json_encode(['sucesso' => false]);
}

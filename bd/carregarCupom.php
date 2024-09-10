<?php
include('conexao.php');

if (isset($_GET['id_cupom'])) {
    $id_cupom = $_GET['id_cupom'];

    // Consulta para buscar os dados do cupom
    $sql = "SELECT ID_CUPOM, CODIGO, DESCONTO, VALIDADE, RESTANTES FROM cupom_desconto WHERE ID_CUPOM = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("i", $id_cupom);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se encontrou o cupom
        if ($row = $result->fetch_assoc()) {
            // Retorna os dados como JSON
            echo json_encode($row);
        } else {
            echo json_encode(null); // Nenhum dado encontrado
        }

        $stmt->close();
    } else {
        echo json_encode(null); // Erro na preparação da query
    }

    $conexao->close();
} else {
    echo json_encode(null); // ID não fornecido
}
?>
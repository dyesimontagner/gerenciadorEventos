<?php
include('conexao.php');

if (isset($_GET['cupom'])) {
    $cupom = $_GET['cupom'];

    // Verificar se o cupom existe no banco de dados
    $sql = "SELECT desconto FROM cupom_desconto WHERE codigo = ?";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        // Bind do parâmetro do cupom
        $stmt->bind_param('s', $cupom);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se o cupom existir, retorna o desconto
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['sucesso' => true, 'desconto' => $row['desconto']]);
        } else {
            // Caso o cupom não exista
            echo json_encode(['sucesso' => false, 'mensagem' => 'Cupom inválido.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na preparação da consulta.']);
    }

    $conexao->close();
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Cupom não informado.']);
}


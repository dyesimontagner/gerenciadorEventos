<?php
include 'conexao.php'; // Certifique-se de que a conexão com o banco de dados está correta

// Query para buscar as formas de pagamento
$sql = "SELECT id, descricao FROM frm_pagamento";
$result = $conexao->query($sql);

$formas_pagamento = array(); // Array para armazenar as formas de pagamento

if ($result->num_rows > 0) {
    // Preencher o array com os resultados
    while($row = $result->fetch_assoc()) {
        $formas_pagamento[] = array('id' => $row['id'], 'descricao' => $row['descricao']);
    }
}

// Retornar os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($formas_pagamento);

// Fechar a conexão
$conexao->close();
?>

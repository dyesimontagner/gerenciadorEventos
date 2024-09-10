<?php
session_start(); // Inicia a sessão

include('conexao.php');

// Consulta para buscar todos os eventos
$query = "SELECT ID_EVENTO, NOME, DATA_INICIO, DATA_FIM, LOCAL, DESCRICAO, IMAGEM FROM evento";
$result = $conexao->query($query);

$eventos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Encode image in base64 if present
        if ($row['IMAGEM']) {
            $row['IMAGEM'] = 'data:image/jpeg;base64,' . base64_encode($row['IMAGEM']);
        } else {
            $row['IMAGEM'] = 'path/to/default-image.jpg'; // Substitua pelo caminho da imagem padrão
        }
        $eventos[] = $row;
    }
}

$conexao->close();

header('Content-Type: application/json');
echo json_encode([
    'eventos' => $eventos
]);
?>
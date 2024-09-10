<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

// Obtém o ID do evento da URL
$eventoId = $_GET['id'] ?? null;

if (!$eventoId) {
    echo json_encode(['error' => 'ID do evento não especificado.']);
    exit();
}

// Prepara e executa a consulta para pegar os dados do evento
$sql = "SELECT 
    e.id_evento, 
    e.nome AS nome_evento, 
    e.data_inicio, 
    e.data_fim, 
    e.local, 
    e.descricao AS descricao_evento, 
    e.imagem, 
    i.preco, 
    i.quantidade,
    i.lkcategoria AS categoria
FROM 
    evento e
INNER JOIN 
    ingressos i ON e.id_evento = i.lkevento
WHERE 
    e.id_evento = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $eventoId);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o evento foi encontrado
if ($result->num_rows > 0) {
    // Pega os dados do evento
    $evento = $result->fetch_assoc();
    // Converte a imagem binária para base64 se estiver presente
    if ($evento['imagem']) {
        $evento['imagem'] = base64_encode($evento['imagem']);
    }
    echo json_encode($evento);
} else {
    echo json_encode(['error' => 'Evento não encontrado.']);
}

// Fecha a conexão
$stmt->close();
$conexao->close();
?>
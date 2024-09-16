<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

// Verifica se a conexão foi estabelecida corretamente
if ($conexao->connect_error) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar com o servidor: ' . $conexao->connect_error]);
    exit;
}

// Captura os dados do formulário
$nome_organizador = $_POST['nome'];
$evento = $_POST['evento'];
$quantidade = $_POST['quantidade'];
$preco = $_POST['preco'];
$forma_pagamento = $_POST['forma_pagamento'];

// Prepara e executa a query de inserção
$sql = "INSERT INTO compras (nome_organizador, evento, quantidade, preco, forma_pagamento) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexao->prepare($sql);
if ($stmt === false) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao preparar a declaração: ' . $conexao->error]);
    exit;
}

$stmt->bind_param("ssdds", $nome_organizador, $evento, $quantidade, $preco, $forma_pagamento);
if ($stmt->execute()) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Compra registrada com sucesso!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar a compra: ' . $stmt->error]);
}

$stmt->close();
$conexao->close();
?>

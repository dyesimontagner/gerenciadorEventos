<?php
session_start();
include('conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT evento.nome, ingresso.data_compra, ingresso.valor 
        FROM ingresso 
        JOIN evento ON ingresso.id_evento = evento.id_evento
        WHERE ingresso.id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$ingressos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ingressos[] = $row;
    }
} else {
    $mensagem = "Nenhum ingresso comprado.";
}

$stmt->close();
$conexao->close();
?>

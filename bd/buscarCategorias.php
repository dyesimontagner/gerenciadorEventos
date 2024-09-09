<?php
session_start(); // Inicia a sessão

include('conexao.php');

// Consulta para buscar as categorias
$query = "SELECT id_categoria, nome FROM categorias";
$result = $conexao->query($query);

$categorias = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}


$user_id = $_SESSION['user_id'] ?? null; 

$organizador = null;
if ($user_id) {
    $queryOrganizador = "SELECT id_organizador FROM organizador WHERE LKUSUARIO = ?";
    if ($stmt = $conexao->prepare($queryOrganizador)) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $organizador = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$conexao->close();

header('Content-Type: application/json');
echo json_encode([
    'categorias' => $categorias,
    'organizador' => $organizador
]);


?>
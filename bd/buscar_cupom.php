<?php
include('conexao.php');

if (isset($_GET['cupom'])) {
    $cupom = $_GET['cupom'];

    // Verificar se o cupom existe no banco de dados
    $sql = "SELECT desconto FROM cupom_desconto WHERE codigo = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind

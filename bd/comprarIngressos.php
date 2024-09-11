<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header('Location: login.php');
    exit();
}

// Processa a compra do ingresso quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome']; // Nome do comprador
    $id_evento = $_POST['evento']; // ID do evento
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $cupom = isset($_POST['cupom']) ? $_POST['cupom'] : null; // Cupom de desconto (opcional)
    $id_usuario = $_SESSION['id_usuario']; // ID do usuário logado

    // Verifica se o cupom é válido
    if ($cupom) {
        $sql_verifica_cupom = "SELECT desconto FROM cupom_desconto WHERE codigo = ?";
        $stmt = $conexao->prepare($sql_verifica_cupom);
        $stmt->bind_param('s', $cupom);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Cupom válido, aplica o desconto
            $row = $result->fetch_assoc();
            $desconto = $row['desconto'] / 100;
            $preco_com_desconto = $preco - ($preco * $desconto);
        } else {
            // Cupom inválido, usa o preço original
            $preco_com_desconto = $preco;
        }
    } else {
        // Sem cupom, usa o preço original
        $preco_com_desconto = $preco;
    }

    // Calcula o valor total
    $valor_total = $preco_com_desconto * $quantidade;

    // Inicia uma transação
    $conexao->begin_transaction();

    try {
        // Verifica se o ingresso já existe na tabela `ingresso`
        $sql_verifica_ingresso = "SELECT id_ingresso FROM ingresso WHERE id_evento = ?";
        $stmt = $conexao->prepare($sql_verifica_ingresso);
        $stmt->bind_param('i', $id_evento);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se o ingresso não existir, inserimos um novo registro na tabela `ingresso`
        if ($result->num_rows === 0) {
            $sql_insere_ingresso = "INSERT INTO ingresso (id_evento, preco) VALUES (?, ?)";
            $stmt = $conexao->prepare($sql_insere_ingresso);
            $stmt->bind_param('id', $id_evento, $preco_com_desconto);
            $stmt->execute();
            // Pega o ID do ingresso recém-inserido
            $id_ingresso = $stmt->insert_id;
        } else {
            // Se o ingresso já existir, capturamos o `id_ingresso`
            $row = $result->fetch_assoc();
            $id_ingresso = $row['id_ingresso'];
        }

        // Insere os dados da compra na tabela `compras`
        $sql_insere_compra = "INSERT INTO compras (id_ingresso, id_usuario, quantidade, valor_total, forma_pagamento, data_compra) 
                              VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conexao->prepare($sql_insere_compra);
        $stmt->bind_param('iiids', $id_ingresso, $id_usuario, $quantidade, $valor_total, $forma_pagamento);

        if ($stmt->execute()) {
            // Confirma a transação
            $conexao->commit();
            echo "Compra realizada com sucesso!";
            echo "<br>Detalhes da Compra:";
            echo "<br>Evento ID: " . $id_evento;
            echo "<br>Quantidade: " . $quantidade;
            echo "<br>Preço Unitário (com desconto, se aplicado): R$ " . number_format($preco_com_desconto, 2, ',', '.');
            echo "<br>Valor Total: R$ " . number_format($valor_total, 2, ',', '.');
            echo "<br>Forma de Pagamento: " . $forma_pagamento;
        } else {
            throw new Exception("Erro ao processar a compra.");
        }

    } catch (Exception $e) {
        // Em caso de erro, desfazemos a transação
        $conexao->rollback();
        echo "Erro ao processar a compra: " . $e->getMessage();
    }

    $stmt->close();
    $conexao->close();
} else {
    echo "Método inválido!";
}
?>


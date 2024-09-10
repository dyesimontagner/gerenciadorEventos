<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    die("Você precisa estar logado para comprar ingressos.");
}

// Processa a compra do ingresso quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $nome_evento = $_POST['evento'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $id_usuario = $_SESSION['id_usuario']; // ID do usuário logado

    // Calcula o valor total
    $valor_total = $preco * $quantidade;

    // Inicia uma transação
    $conexao->begin_transaction();

    try {
        // Verifica se o ingresso já existe na tabela `ingresso`
        $sql_verifica_ingresso = "SELECT id_ingresso FROM ingresso WHERE nome_evento = ?";
        $stmt = $conexao->prepare($sql_verifica_ingresso);
        $stmt->bind_param('s', $nome_evento);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se o ingresso não existir, inserimos um novo registro na tabela `ingresso`
        if ($result->num_rows === 0) {
            $sql_insere_ingresso = "INSERT INTO ingresso (nome_evento, preco) VALUES (?, ?)";
            $stmt = $conexao->prepare($sql_insere_ingresso);
            $stmt->bind_param('sd', $nome_evento, $preco);
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
            echo "<br>Evento: " . $nome_evento;
            echo "<br>Quantidade: " . $quantidade;
            echo "<br>Preço Unitário: R$ " . number_format($preco, 2, ',', '.');
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

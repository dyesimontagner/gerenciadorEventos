<?php
// Inclua a conexão com o banco de dados
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cupom = $_POST['id_cupom']; // Obtém o ID do cupom enviado via POST

    // Verifica se o ID do cupom foi passado
    if (isset($id_cupom)) {
        // Query para atualizar o cupom e zerar os cupons restantes
        $sql = "UPDATE cupom_desconto SET RESTANTES = 0 WHERE ID_CUPOM = ?";
        
        // Prepara a query
        if ($stmt = $conexao->prepare($sql)) {
            $stmt->bind_param("i", $id_cupom); // Vincula o parâmetro ID_CUPOM
            if ($stmt->execute()) {
                echo "Cupom descontinuado com sucesso!";
            } else {
                echo "Erro ao descontinuar o cupom: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Erro na preparação da query: " . $conexao->error;
        }
    } else {
        echo "ID do cupom não fornecido!";
    }

    $conexao->close();
}
?>
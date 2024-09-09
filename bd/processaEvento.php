<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dados do evento
    $pid_evento = isset($_POST['id_evento']) ? (int)$_POST['id_evento'] : 0;
    $nome_evento = $_POST['nome_evento'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $local = $_POST['local'];
    $descricao_evento = $_POST['descricao_evento'];

    // Dados dos ingressos
    $quantidade_ingressos = $_POST['quantidade_ingressos'];
    $preco_ingresso = $_POST['preco_ingresso'];
    $categoria_ingresso = $_POST['categoria_ingresso'];
    $id_organizador = $_POST['id_organizador'];
    // Lê a imagem como binário se estiver presente
    $imagem = null;
    if (isset($_FILES['imagem_evento']) && $_FILES['imagem_evento']['error'] === UPLOAD_ERR_OK) {
        $imagem = file_get_contents($_FILES['imagem_evento']['tmp_name']);
    }
    
    // Exemplo de chamada à procedure (ajustar conforme sua necessidade):
    $sql = "CALL SP_EVENTOIA(?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conexao->prepare($sql)) {
        
        $stmt->bind_param("isssssib", 
            $pid_evento,         // ID do evento (0 para inserção)
            $nome_evento,        // Nome do evento
            $data_inicio,        // Data de início
            $data_fim,           // Data de fim
            $local,              // Local do evento
            $descricao_evento,    // Descrição do evento
            $id_organizador,        // id do organizador
            $imagem  // Placeholder para imagem (definido como null)
       
        );
        
        
        // Se houver uma imagem, envia os dados binários
        if ($imagem !== null) {
            try {
                $stmt->send_long_data(6, $imagem);
            } catch (Exception $e) {
                echo 'Erro: ' . $e->getMessage();
            }
        }
        
        if ($stmt->execute()) {
            //pega o último id do evento
            $result = $conexao->query("SELECT MAX(id_evento) AS id_evento FROM evento");
            if ($result && $row = $result->fetch_assoc()) {
                $last_evento_id = $row['id_evento'];
            } else {
                echo "Erro ao obter o ID do evento.";
            }

            $sql_ingresso = "CALL SP_INGRESSOSIA(?, ?, ?, ?, ?)";
            if ($stmt_ingresso = $conexao->prepare($sql_ingresso)) {
                $pid_ingresso = 0;
                $stmt_ingresso->bind_param("iidsi",
                    $pid_ingresso,         // ID do ingresso (0 para inserção)
                    $last_evento_id,        // ID do evento (relacionado)
                    $preco_ingresso,       // Preço do ingresso
                    $categoria_ingresso,    // Categoria do ingresso
                    $quantidade_ingressos // Quantidade de ingressos
                );
                if ($stmt_ingresso->execute()) {
                    echo "Evento e ingressos cadastrados com sucesso!";
                } else {
                    echo "Erro ao cadastrar ingressos: " . $stmt_ingresso->error;
                }
            }
        } else {
            echo "Erro ao cadastrar evento: " . $stmt->error;
        }

        $stmt->close();
    
    } else {
        echo "Erro na preparação da query: " . $conexao->error;
    }

    $conexao->close();
}
?>
<?php 
$servidor = "localhost";
$usuario = "root";
$senha = "";
$bd = "PROJETOBD";

$conn = new mysqli($servidor, $usuario, $senha, $bd);


if ($conn->connect_error) {
    die('Falha na conxão: ' .$conn->connect_error);
}



?>
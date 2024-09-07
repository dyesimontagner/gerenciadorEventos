<?php 
$servidor = "localhost";
$usuario = "root";
$senha = "";
$bd = "PROJETOBD";

$conec = new mysqli($servidor, $usuario, $senha, $bd);


if ($conec->connect_error) {
    die('Falha na conxão: ' .$conec->connect_error);
}



?>
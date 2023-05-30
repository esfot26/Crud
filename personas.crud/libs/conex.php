<?php
$servidor="localhost"; // ip o direccion de la base de datos
$usuario="dw2_user";
$password="dw2_user";
$base="ciudades";
$conn=mysqli_connect($servidor,$usuario,$password,$base);
if($conn->connect_error)
{
    die("fallo la conexion");
}
    //echo "conectado";

?>
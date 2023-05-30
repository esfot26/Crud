<?php
include("libs/conex.php");
include("libs/personas.lib.php");
include("libs/ciudades.lib.php");

function obtenerNombreCiudad($ciudad_id, $con) {
    $ciudades = obtenerCiudades($con);

    // Buscar el nombre de la ciudad por su ID en el array de ciudades
    foreach ($ciudades as $ciudad) {
        if ($ciudad['id'] == $ciudad_id) {
            return $ciudad['nombre'];
        }
    }

    return null; // Si no se encuentra la ciudad, se puede retornar null o un valor por defecto
}

if ($_POST) {
    if ($_POST['nombre'] && $_POST['apellido'] && $_POST['cin'] && $_POST['direccion'] && $_POST['fecha_nac'] && $_POST['ciudad_id']) {
        $ciudad_id = $_POST['ciudad_id'];
        $ciudad_nombre = obtenerNombreCiudad($ciudad_id, $conn);
        
        if ($ciudad_nombre) {
            $_POST['ciudad_nombre'] = $ciudad_nombre;
            guardarPersona($_POST, $conn);
        } else {
            echo 'Error: No se encontró el nombre de la ciudad.';
        }
    }
}

echo '<p>Soy guardar</p>';

header('Location: index.php');
?>

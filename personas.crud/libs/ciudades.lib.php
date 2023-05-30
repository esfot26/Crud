<?php
function traerCiudades($con)
{
    $sql="SELECT * FROM ciudades";
    $filas=$con->query($sql);
    return $filas;
}

function traerCiudad($id,$con)
{
    $sql="SELECT * FROM ciudades where id=".$id;
    $filas=$con->query($sql);
    return $filas;
}

function agregarCiudad($datos,$con)
{
    $sql="INSERT INTO ciudades (nombre) VALUES ('".$datos['nombre']."');";
    $con->query($sql);
}


// libs/ciudades.lib.php

function obtenerCiudades($con) {
    $ciudades = array();

    // Consulta SQL para obtener las ciudades
    $sql = "SELECT id, nombre FROM ciudades";
    $result = $con->query($sql);

    if ($result) {
        // Recorrer el resultado y almacenar los datos en el array de ciudades
        while ($row = $result->fetch_assoc()) {
            $ciudades[] = $row;
        }
    }

    return $ciudades;
}



function guardarCiudad($datos,$con)
{
    $sql="INSERT INTO ciudades (nombre) VALUES ('".$datos['nombre']."');";
    $con->query($sql);
}

function editarCiudad($datos, $con)
{
    $sql = "UPDATE ciudades SET nombre = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('si', $datos['nombre'], $datos['id']);
    $stmt->execute();
    $stmt->close();
}



function borrarCiudad($id,$con)
{
   // delete from ciudades where id == ID
    $sql="delete from  ciudades where id=".$id;
    $filas=$con->query($sql);
    return $filas;
}
?>
<?php
include("libs/conex.php");
include("libs/ciudades.lib.php");
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los valores del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cin = $_POST['cin'];
    $direccion = $_POST['direccion'];
    $fecha_nac = $_POST['fecha_nac'];
    $ciudad_id = $_POST['ciudad_id'];

    // Verificar si los campos están vacíos
    if (strlen($nombre) < 2) {
        $errors[] = 'El nombre debe tener al menos 2 caracteres';
    }
    if (strlen($apellido) < 2) {
        $errors[] = 'El apellido debe tener al menos 2 caracteres';
    }
    if (!preg_match('/^[0-9]{7}$/', $cin)) {
        $errors[] = 'El CIN debe contener exactamente 7 dígitos';
    }
    if (strlen($direccion) < 5) {
        $errors[] = 'La dirección debe tener al menos 5 caracteres';
    }
    if (empty($fecha_nac)) {
        $errors[] = 'La fecha de nacimiento es obligatoria';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $fecha_nac);
        if (!$date || $date->format('Y-m-d') !== $fecha_nac) {
            $errors[] = 'La fecha de nacimiento no es válida';
        }
    }
    if (empty($ciudad_id)) {
        $errors[] = 'Debe seleccionar una ciudad';
    } else {
        // Verifica si la ciudad existe en la base de datos
        $ciudad = obtenerCiudadPorId($ciudad_id, $conn);
        if (!$ciudad) {
            $errors[] = 'La ciudad seleccionada no es válida';
        }
    }

    // Si no hay errores, guardar los datos
    if (empty($errors)) {
        $persona = guardarPersona($nombre, $apellido, $cin, $direccion, $fecha_nac, $ciudad_id, $conn);
        if ($persona) {
            echo "Los datos se guardaron correctamente";
        } else {
            echo "Error al guardar los datos";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = validarCampo($_POST["nombre"]);
    $apellido = validarCampo($_POST["apellido"]);
    $cin = validarCampo($_POST["cin"]);
    $direccion = validarCampo($_POST["direccion"]);
    $fecha_nac = validarCampo($_POST["fecha_nac"]);
    $ciudad_id = validarCampo($_POST["ciudad_id"]);

    // guardar los datos si no hay errores de validación
    if (!empty($nombre) && !empty($apellido) && !empty($cin) && !empty($direccion) && !empty($fecha_nac) && !empty($ciudad_id)) {

        // redireccionar a otra página después de guardar los datos
        header("Location: otra_pagina.php");
        exit();
    } else {

        echo "Error: Por favor, complete todos los campos.";
    }
}

// función para validar un campo y evitar posibles problemas de seguridad
function validarCampo($campo) {
    $campo = trim($campo);
    $campo = stripslashes($campo);
    $campo = htmlspecialchars($campo);
    return $campo;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlace a los archivos CSS de Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Formulario</title>
</head>
<body style="background-color:powderblue;">
    <div class="container">
        <h3 style="color:black;">Agregar persona</h3>
        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger" role="alert">';
            foreach ($errors as $error) {
                echo '<p>' . $error . '</p>';
            }
            echo '</div>';
        }
        ?>
        <form action="guardar.php" method="post">
            <div class="form-group">
                <label for="nombre" style="color:black;">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido" style="color:black;">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="cin" style="color:black;">CIN</label>
                <input type="text" class="form-control" id="cin" name="cin" required>
            </div>
            <div class="form-group">
                <label for="direccion" style="color:black;">Direccion</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required>
            </div>
            <div class="form-group">
                <label for="fecha_nac" style="color:black;">Fecha de nacimiento</label>
                <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" required>
            </div>
            <div class="form-group">
                <label for="ciudad_id" style="color:black;">Ciudad</label>
                <select class="form-control" id="ciudad_id" name="ciudad_id">
                    <option value="">Seleccione una ciudad</option>
                    <?php
                    $ciudades = obtenerCiudades($conn);
                    foreach ($ciudades as $ciudad) {
                        echo '<option value="' . $ciudad['id'] . '">' . $ciudad['nombre'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>

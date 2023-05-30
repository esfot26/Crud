<?php
// Conexión a la base de datos
include("libs/conex.php");
include("libs/personas.lib.php");
include("libs/ciudades.lib.php");
$id = $_GET['id'];
// Consulta SQL para obtener los datos del registro a editar
$sql = "SELECT * FROM personas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Variable para almacenar el estado de edición exitosa
$editSuccess = false;

// Verificar si se ha enviado el formulario de edición
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los nuevos valores del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cin = $_POST['cin'];
    $direccion = $_POST['direccion'];
    $fecha_nac = $_POST['fecha_nac'];
    $ciudad_id = $_POST['ciudad_id'];

    // Realizar la validación de datos
    $errors = validarDatos($_POST, $conn);

    if (!empty($errors)) {
        // Mostrar errores al usuario o realizar alguna acción necesaria
        foreach ($errors as $error) {
            //echo '<p>' . htmlspecialchars($error) . '</p>';
        }
    } else {
        // Actualizar los datos en la base de datos utilizando la función editarPersona()
        $datos = array(
            'id' => $id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'cin' => $cin,
            'direccion' => $direccion,
            'fecha_nac' => $fecha_nac,
            'ciudad_id' => $ciudad_id
        );
        editarPersona($datos, $conn);

// Establecer el estado de edición exitosa
        $editSuccess = true;
    }
}
function obtenerNombreCiudad($id, $conn)
{
    $sql = "SELECT nombre FROM ciudades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->fetch();
    $stmt->close();

    return $nombre;
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <title>Editar Persona</title>
</head>
<body style="background-color:powderblue;">
    <div class="container">
        <h1 style="color:black;">Editar Persona</h1>
        <?php if ($editSuccess) { ?>
            <div class="alert alert-success" role="alert">
                Edición exitosa
            </div>
            <script>
                // Mostrar alerta de edición exitosa durante 3 segundos
                setTimeout(function() {
                    window.location.href = "index.php";  // Redirigir al index.php
                }, 3000);
            </script>
        <?php } ?>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error) { ?>
                    <p><?php echo $error; ?></p>
                <?php } ?>
            </div>
        <?php } ?>
        <form method="POST">
            <div class="form-group">
                <label for="nombre" style="color:black;">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $row['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido" style="color:black;">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $row['apellido']; ?>" required>
            </div>
            <div class="form-group">
                <label for="cin" style="color:black;">CIN</label>
                <input type="text" class="form-control" id="cin" name="cin" value="<?php echo $row['cin']; ?>" required>
            </div>
            <div class="form-group">
                <label for="direccion" style="color:black;">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $row['direccion']; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_nac" style="color:black;">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" value="<?php echo $row['fecha_nac']; ?>" required>
            </div>
            <div class="form-group">
                <label for="ciudad_id" style="color:black;">Ciudad</label>
                <select class="form-control" id="ciudad_id" name="ciudad_id">
                    <?php
                    // Obtener las ciudades desde la base de datos
                    $ciudades = obtenerCiudades($conn);
                    // Recorrer el resultado y crear las opciones de la lista desplegable
                    foreach ($ciudades as $ciudad) {
                        $selected = ($ciudad['id'] == $row['ciudad_id']) ? 'selected' : '';
                        echo '<option value="' . $ciudad['id'] . '" ' . $selected . '>' . $ciudad['nombre'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirmarEdicion()">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </form>
        <script>
        function confirmarEdicion() {
            return confirm("¿Estás seguro de que deseas guardar los cambios?");
        }
    </script>
    </div>
</body>
</html>

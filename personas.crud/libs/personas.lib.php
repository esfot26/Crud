<?php
function traerPersonas($conn) {
    $sql = "SELECT * FROM personas";
    $result = $conn->query($sql);

    $personas = array();

    while ($row = $result->fetch_assoc()) {
        $personas[] = $row;
    }

    return $personas;
}

function traerPersona($id, $con)
{
    $sql = "SELECT * FROM personas WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fila = $result->fetch_assoc();
        return $fila;
    } else {
        return null;
    }
}

function validarDatos($datos, $conn)
{
    $errors = [];
    // Realiza las validaciones específicas en los datos
    if (strlen($datos['nombre']) < 2) {
        $errors[] = 'El nombre debe tener al menos 2 caracteres';
    }
    if (strlen($datos['apellido']) < 2) {
        $errors[] = 'El apellido debe tener al menos 2 caracteres';
    }
    if (!preg_match('/^[0-9]{7}$/', $datos['cin'])) {
        $errors[] = 'El CIN debe contener al menos 7 dígitos';
    }
    if (strlen($datos['direccion']) < 5) {
        $errors[] = 'La dirección debe tener al menos 5 caracteres';
    }
    if (empty($datos['fecha_nac'])) {
        $errors[] = 'La fecha de nacimiento es obligatoria';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $datos['fecha_nac']);
        if (!$date || $date->format('Y-m-d') !== $datos['fecha_nac']) {
            $errors[] = 'La fecha de nacimiento no es válida';
        }
    }
    if (empty($datos['ciudad_id'])) {
        $errors[] = 'La ciudad es obligatoria';
    } else {
        // Verifica si la ciudad existe en la base de datos
        $ciudadId = $datos['ciudad_id'];

        // Realiza la consulta para verificar la existencia de la ciudad en la base de datos
        $sql = "SELECT COUNT(*) FROM ciudades WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $ciudadId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $errors[] = 'La ciudad seleccionada no existe';
        }
    }

    return $errors;
}

function obtenerCiudadPorId($ciudad_id, $conn) {
    $sql = "SELECT * FROM ciudades WHERE id = $ciudad_id";
    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $ciudad = mysqli_fetch_assoc($resultado);
        return $ciudad;
    }

    return null;
}

function editarPersona($datos, $conn)
{
    $sql = "UPDATE personas SET nombre = ?, apellido = ?, cin = ?, direccion = ?, fecha_nac = ?, ciudad_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssi', $datos['nombre'], $datos['apellido'], $datos['cin'], $datos['direccion'], $datos['fecha_nac'], $datos['ciudad_id'], $datos['id']);
    $stmt->execute();
    $stmt->close();
}

function guardarPersona($datos, $con)
{
    try {
        // Verificar la conexión a la base de datos
        if (!$con instanceof mysqli || $con->connect_error) {
            throw new Exception('No se pudo establecer la conexión a la base de datos.');
        }
        $nombre = $datos['nombre'];
        $apellido = $datos['apellido'];
        $cin = $datos['cin'];
        $direccion = $datos['direccion'];
        $fecha_nac = $datos['fecha_nac'];
        $ciudad_id = $datos['ciudad_id'];

        // Insertar persona en la base de datos
        $sql = "INSERT INTO personas (nombre, apellido, cin, direccion, fecha_nac, ciudad_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $con->error);
        }
        $stmt->bind_param('sssssi', $nombre, $apellido, $cin, $direccion, $fecha_nac, $ciudad_id);
        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        // Obtener el contenido de la tabla "ciudades" usando el ID
        $ciudad_sql = "SELECT * FROM ciudades WHERE id = ?";
        $ciudad_stmt = $con->prepare($ciudad_sql);
        if (!$ciudad_stmt) {
            throw new Exception('Error al preparar la consulta: ' . $con->error);
        }
        $ciudad_stmt->bind_param('i', $ciudad_id);
        if (!$ciudad_stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $ciudad_stmt->error);
        }
        $ciudad_result = $ciudad_stmt->get_result();
        if (!$ciudad_result) {
            throw new Exception('Error al obtener los resultados de la consulta: ' . $ciudad_stmt->error);
        }
        $ciudad_data = $ciudad_result->fetch_assoc();

        echo 'La persona se ha agregado correctamente.';
        echo 'Ciudad: ' . $ciudad_data['nombre']; // Ejemplo de cómo acceder al nombre de la ciudad

    } catch (Exception $e) {
        // Manejar errores
        echo 'Error al agregar la persona: ' . htmlspecialchars($e->getMessage());
    }
}

function borrarPersona($id,$con)
{
   // delete from ciudades where id == ID
    $sql="delete from  personas where id=".$id;
    $filas=$con->query($sql);
    return $filas;
}
?>
<?php
include("libs/conex.php");
include("libs/personas.lib.php");
$datos = traerPersonas($conn);
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
// Assuming you have already established a database connection using mysqli_connect()

$query = "SELECT * FROM personas";
$result = mysqli_query($conn, $query);

if ($result) {
  $numRows = mysqli_num_rows($result);
  //echo "Number of rows: " . $numRows;
} else {
  echo "Query execution failed.";
}

// Obtener el término de búsqueda desde la URL
$termino_busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Filtrar los registros según el término de búsqueda
if (!empty($termino_busqueda)) {
    $datos_filtrados = [];
    foreach ($datos as $d) {
        if (stripos($d['nombre'], $termino_busqueda) !== false ||
            stripos($d['apellido'], $termino_busqueda) !== false ||
            $d['cin'] == $termino_busqueda ||
            stripos(obtenerNombreCiudad($d['ciudad_id'], $conn), $termino_busqueda) !== false
        ) {
            $datos_filtrados[] = $d;
        }
    }
    $datos = $datos_filtrados;
}
// Paginación es para que al completar un determinado numero de registros se añadan mas paginas
$registros_por_pagina = 8; // Número de registros por página
$total_registros = count($datos); // Total de registros
$total_paginas = ceil($total_registros / $registros_por_pagina); // Total de páginas

$pagina_actual = isset($_GET['page']) ? $_GET['page'] : 1; // Página actual
$inicio = ($pagina_actual - 1) * $registros_por_pagina; // Índice de inicio
$datos_paginados = array_slice($datos, $inicio, $registros_por_pagina); // Registros de la página actual


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Personas</title>
</head>
<body style="background-color:powderblue;">
    <div class="container">
        <h1 style="color:black;">Personas</h1>
        <form action="index.php" method="GET">
            <div class="form-group">
              <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, apellido, CIN o ciudad" value="<?php echo htmlentities($termino_busqueda); ?>"><br>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
        <?php
        if ($_GET && isset($_GET['mod']) && $_GET['mod'] == 'delpersona' && isset($_GET['id'])) {
            $id = $_GET['id'];
            borrarPersona($id, $conn);
            header('Location: index.php');
            exit();
        }
        ?>
        <form action="index.php" method="GET">
            <div class="form-group">
            <br> <a href="nuevo.php" class="btn btn-primary">Nuevo</a><br>
            </form>
        <br>
        <table class="table table-dark">
            <thead class="thead-dark">
                <tr class="bg-info">
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cin</th>
                    <th>Dirección</th>
                    <th>Fecha de nacimiento</th>
                    <th>Ciudad</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($datos_paginados as $d): ?>
                <tr>
                    <td class="table-dark"><?php echo $d['id']; ?></td>
                    <td class="table-defalut"><?php echo $d['nombre']; ?></td>
                    <td class="table-defalut"><?php echo $d['apellido']; ?></td>
                    <td class="table-defalut"><?php echo $d['cin']; ?></td>
                    <td class="table-defalut"><?php echo $d['direccion']; ?></td>
                    <td class="table-defalut"><?php echo $d['fecha_nac']; ?></td>
                    <td class="table-defalut">
                        <?php
                        $ciudad_id = $d['ciudad_id'];
                        $ciudad_nombre = obtenerNombreCiudad($ciudad_id, $conn);
                        echo $ciudad_nombre;
                        ?>
                    </td>
                    <td class="table-defalut">
                        <a href="editar.php?id=<?php echo $d['id']; ?>" class="btn btn-success">Editar</a>
                    </td>
                    <td class="table-defalut">
                        <a href="#" class="btn btn-danger" onclick="confirmarBorrado(<?php echo $d['id']; ?>);">Borrar</a>
                        <script>
                            function confirmarBorrado(id) {
                                if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                                    window.location.href = "index.php?mod=delpersona&id=" + id;
                                }
                            }
                        </script>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav aria-label="Navegación paginada">
            <ul class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?php echo $pagina_actual - 1; ?>&busqueda=<?php echo $termino_busqueda; ?>">Anterior</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=<?php echo $i; ?>&busqueda=<?php echo $termino_busqueda; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?php echo $pagina_actual + 1; ?>&busqueda=<?php echo $termino_busqueda; ?>">Siguiente</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</body>
</html>

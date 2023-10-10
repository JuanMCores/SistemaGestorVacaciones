<?php
require 'conexionbd.php';
require 'verificar_sesion.php';

// Variable para almacenar el término de búsqueda
$estadoBuscado = '';

// Verificar si se proporciona el ID del empleado en la URL
if (isset($_GET["id"])) {
    $employeeId = $_GET["id"];
} else {
    // Si no se proporciona el ID, redirige a la página de mostrarvacaciones_administrador.php
    header("Location: mostrarvacaciones_administrador.php");
    exit;
}

// Verificar si se proporciona el filtro de estado en la URL
if (isset($_GET["estado"])) {
    $estadoBuscado = $_GET["estado"];
}

// Consulta SQL para seleccionar las solicitudes de vacaciones y los datos del empleado en función de su ID
$query = "SELECT sv.id, sv.days, sv.fecha_inicio, sv.fecha_end, sv.estado, e.name, e.lastname, e.dni 
          FROM solicitudes_vacaciones sv 
          INNER JOIN empleados e ON sv.empleado_id = e.id 
          WHERE sv.empleado_id = ?";

// Agregar la cláusula WHERE para filtrar por estado si se especifica un término de búsqueda
if (!empty($estadoBuscado)) {
    $query .= " AND sv.estado LIKE '%$estadoBuscado%'";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

// Consulta SQL para obtener el nombre del empleado actual en función de su ID
$queryNombreEmpleado = "SELECT name FROM empleados WHERE id = ?";
$stmtNombreEmpleado = $conn->prepare($queryNombreEmpleado);
$stmtNombreEmpleado->bind_param("i", $employeeId);
$stmtNombreEmpleado->execute();
$resultNombreEmpleado = $stmtNombreEmpleado->get_result();

if ($resultNombreEmpleado->num_rows > 0) {
    $nombreEmpleado = $resultNombreEmpleado->fetch_assoc()["name"];
} else {
    $nombreEmpleado = "Empleado Desconocido";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Vacaciones</title>
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mostrardatos.css">
    <link rel="stylesheet" href="css/navbar.css">

    <style>
        body {
            background-image: url('img/minimal-wallpaper.png');
            background-size:cover;
           
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: #ffffff;
        }

        .table th {
            background-color: #343a40;
            color: #ffffff;
        }

        .data-row {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<header>
    <h2 class="logo">
    <?php
        if (isset($_SESSION["nombre"])) {
            echo "Bienvenido ".$_SESSION["nombre"];
        } else {
            echo "Bienvenido Administrador";
        }
    ?>
    </h2>
    <nav class="navigation">
        <a href="mostrardatos.php">Volver</a>
        <a href="index.php">Cerrar Sesión</a>
    </nav>
</header>

<div class="container mt-5">
    <h2>Solicitudes de Vacaciones</h2>

    <div class="float-right mb-3">
        <form method="get" action="">
            <!-- Mantén el ID del empleado en el formulario oculto -->
            <input type="hidden" name="id" value="<?= $employeeId ?>">
            <div class="input-group">
            <select name="estado" id="filtroEstado" class="form-control">
                <option value="Pendiente"<?= $estadoBuscado === 'Pendiente' ? ' selected' : '' ?>>Pendiente</option>
                <option value="Aprobado"<?= $estadoBuscado === 'Aprobado' ? ' selected' : '' ?>>Aprobado</option>
                <option value="Rechazado"<?= $estadoBuscado === 'Rechazado' ? ' selected' : '' ?>>Rechazado</option>
            </select>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>

    <?php
if ($result->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Nombre</th>";
    echo "<th>Apellido</th>";
    echo "<th>DNI</th>";
    echo "<th>Días de Vacaciones</th>";
    echo "<th>Fecha de Inicio</th>";
    echo "<th>Fecha de Fin</th>";
    echo "<th>Estado</th>"; // Agregar columna para el estado
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr id='tablaVacaciones'";
        // Aplicar el estilo de fondo y color de texto según el estado
        $backgroundColor = '';
        $textColor = '';
        switch ($row["estado"]) {
            case "Pendiente":
                $backgroundColor = 'yellow';
                $textColor = 'black';
                break;
            case "Rechazado":
                $backgroundColor = 'red';
                $textColor = 'white';
                break;
            case "Aprobado":
                $backgroundColor = 'green';
                $textColor = 'white';
                break;
            default:
                // Otros estados
                break;
        }
        echo ">";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["lastname"] . "</td>";
        echo "<td>" . $row["dni"] . "</td>";
        echo "<td>" . $row["days"] . "</td>";
        echo "<td>" . $row["fecha_inicio"] . "</td>";
        echo "<td>" . $row["fecha_end"] . "</td>";
        echo "<td id='estado-" . $row["id"] . "' style='background-color: $backgroundColor; color: $textColor;'>" . $row["estado"] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<div class='alert alert-warning' role='alert'>";
    echo "No hay vacaciones asignadas para este empleado.";
    echo "</div>";
}
?>

</div>
</body>
</html>

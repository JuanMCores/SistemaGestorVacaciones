<?php
require 'conexionbd.php';
require 'verificar_sesion.php';

// Verificar si se proporciona el ID de la solicitud de vacaciones
if (isset($_GET["id"])) {
    $vacationId = $_GET["id"];
} else {
    // Si no se proporciona el ID, redirige a la página de solicitudesVacaciones_administrador.php
    header("Location: solicitudesVacaciones_administrador.php");
    exit;
}

// Variable para almacenar el mensaje de confirmación
$confirmationMessage = "";

// Verificar si se ha enviado el formulario para modificar vacaciones
if (isset($_POST["modificar"])) {
    $days = $_POST["days"];
    $fecha_inicio = $_POST["fecha_inicio"];
    
    // Calcular la fecha de finalización sumando los días a la fecha de inicio
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_end_obj = clone $fecha_inicio_obj;
    $fecha_end_obj->modify("+" . $days . " days");
    $fecha_end = $fecha_end_obj->format("Y-m-d");
    
    // Actualizar los datos de la solicitud de vacaciones en la tabla de solicitudes_vacaciones
    $updateQuery = "UPDATE solicitudes_vacaciones SET days = ?, fecha_inicio = ?, fecha_end = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("issi", $days, $fecha_inicio, $fecha_end, $vacationId);
    
    if ($updateStmt->execute()) {
        $confirmationMessage = "Solicitud de vacaciones modificada exitosamente.";
        
        // Redireccionar después de mostrar el mensaje de confirmación
        header("Location: solicitudesVacaciones_administrador.php");
        exit;
    } else {
        $confirmationMessage = "Error al modificar la solicitud de vacaciones: " . $conn->error;
    }
}

// Consulta SQL para obtener los datos de la solicitud de vacaciones actual
$query = "SELECT sv.days, sv.fecha_inicio, e.name, e.lastname, e.dni, e.anios_ingreso FROM solicitudes_vacaciones sv 
          INNER JOIN empleados e ON sv.empleado_id = e.id 
          WHERE sv.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vacationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $vacationData = $result->fetch_assoc();
    $employeeName = $vacationData["name"];
    $employeeLastname = $vacationData["lastname"];
    $employeeDNI = $vacationData["dni"];
    $employeeAnios_ingreso = $vacationData["anios_ingreso"];
    $vacationDays = $vacationData["days"];
    $vacationFechaInicio = $vacationData["fecha_inicio"];
} else {
    echo "<script>alert('Solicitud de vacaciones no encontrada.');</script>";
    header("Location: mostrarTodasVacaciones.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Vacaciones de Empleado</title>
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/editar-asignar_vacaciones.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<header>
    <h2 class="logo">
    <?php
        if (isset($_SESSION["nombre"])) {
            echo $_SESSION["nombre"];
        } else {
            echo "Administrador";
        }
    ?>
    </h2>
    <nav class="navigation">
    <a href="solicitudesVacaciones_administrador.php">Volver</a>
        <a href="index.php">Cerrar Sesión</a>
    </nav>
</header>

    <div class="container mt-5">
        <h2>Modificar Solicitud de Vacaciones</h2><br>
        <p><b>Nombre:</b><span class="pad"><?php echo $employeeName; ?></span></p>
        <p><b>Apellido:</b><span class="pad"><?php echo $employeeLastname; ?></span></p>
        <p><b>DNI:</b><span class="pad"><?php echo $employeeDNI; ?></span></p>
        <p><b>Años de Ingreso:</b><span class="pad"><?php echo $employeeAnios_ingreso; ?></span></p>

        <!-- Mostrar el mensaje de confirmación aquí -->
        <?php if (!empty($confirmationMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $confirmationMessage; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="calendar-number"></ion-icon>
                </span>
                <label for="days"><b>Días de Vacaciones</b></label>
                <select id="days" name="days">
                    <!-- Opciones de días de vacaciones se generan dinámicamente con JavaScript -->
                </select>
            </div><br>
            
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required min="<?php echo date('Y-m-d'); ?>">
            </div><br>

            <button type="submit" class="btn btn-primary" name="modificar">Guardar Modificaciones</button>
        </form>

        <!-- Script para generar las opciones de "days" basadas en "aniosIngreso" -->
        <script>
            const aniosIngreso = <?php echo $employeeAnios_ingreso; ?>;
            const selectDays = document.getElementById("days");

            for (let i = 1; i <= aniosIngreso; i++) {
                const option = document.createElement("option");
                option.value = i * 7; // Multiplica los años por 7 para obtener el número de días
                option.textContent = (i * 7) + " días"; // Agrega "días" al final
                selectDays.appendChild(option);
            }

            // Establecer el valor seleccionado en el elemento select
            selectDays.value = <?php echo $vacationDays; ?>;
        </script>
    </div>
</body>
</html>

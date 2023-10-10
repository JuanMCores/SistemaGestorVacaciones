<?php
require 'conexionbd.php'; // Asegúrate de tener este archivo para la conexión a la base de datos.
require 'verificar_sesion.php';

// Verificar si se proporciona el ID del empleado
if (isset($_GET["id"])) {
    $employeeId = $_GET["id"];
} else {
    // Si no se proporciona el ID, redirige a la página de mostrar_empleados.php
    header("Location: mostrar_empleados.php");
    exit;
}

// Variable para almacenar el mensaje de confirmación
$confirmationMessage = "";

// Verificar si se ha enviado el formulario para asignar vacaciones
if (isset($_POST["asignar"])) {
    $days = $_POST["days"];
    $fecha_inicio = $_POST["fecha_inicio"];
    $fecha_end = $_POST["fecha_inicio"];
    
    // Calcular la fecha de finalización sumando los días a la fecha de inicio
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_end_obj = clone $fecha_inicio_obj;
    $fecha_end_obj->modify("+" . $days . " days");
    $fecha_end = $fecha_end_obj->format("Y-m-d");
    
    // Verificar si ya existe una solicitud de vacaciones con la misma fecha de inicio
    $verificarQuery = "SELECT id FROM solicitudes_vacaciones WHERE empleado_id = ? AND fecha_inicio = ?";
    $verificarStmt = $conn->prepare($verificarQuery);
    $verificarStmt->bind_param("is", $employeeId, $fecha_inicio);
    $verificarStmt->execute();
    $verificarResult = $verificarStmt->get_result();

    if ($verificarResult->num_rows > 0) {
        $confirmationMessage = "Ya existe una solicitud de vacaciones con la misma fecha de inicio.";
    } else {
        // Insertar los datos de vacaciones en la tabla de solicitudes_vacaciones con estado "pendiente"
        $estado = "Pendiente"; // Estado inicial
        $insertQuery = "INSERT INTO solicitudes_vacaciones (empleado_id, days, fecha_inicio, fecha_end, estado) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iisss", $employeeId, $days, $fecha_inicio, $fecha_end, $estado);
        
        if ($insertStmt->execute()) {
            $confirmationMessage = "Solicitud de vacaciones insertada exitosamente.";
            
            // Redireccionar después de mostrar el mensaje de confirmación
            header("Location: index.php");
            exit;
        } else {
            $confirmationMessage = "Error al insertar la solicitud de vacaciones: " . $conn->error;
        }
    }
}

// Consulta SQL para obtener los datos del empleado
$query = "SELECT name, lastname, dni, anios_ingreso FROM empleados WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employeeData = $result->fetch_assoc();
    $employeeName = $employeeData["name"];
    $employeeLastname = $employeeData["lastname"]; // Agrega esta línea para obtener el apellido
    $employeeDNI = $employeeData["dni"];
    $employeeAnios_ingreso = $employeeData["anios_ingreso"];
} else {
    echo "<script>alert('Empleado no encontrado.');</script>";
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaciones de Empleado</title>
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/editar-asignar_vacaciones.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body style="background-image:url('img/minimal-wallpaper.png');background-size:cover;">
<header>
    <h2 class="logo">
    <?php
        if (isset($employeeName,$employeeLastname)) {
            echo "Bienvenido ".$employeeName." ".$employeeLastname;
        } else {
            echo "Administrador";
        }
    ?>
    </h2>
    
        

        <nav class="navigation">
        <!-- <a href="mostrardatos.php">Volver</a> -->
        <a href="index.php">Cerrar Sesión</a>
    </nav>
  
</header>


    <div class="container mt-5" style="background-color:white;opacity:0.85;border-radius: 20px;">
        <h2>Asignar Solicitud de Vacaciones</h2><br>
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


            <button type="submit" class="btn btn-primary" name="asignar">Guardar Vacaciones</button>
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
        </script>

    </div>
</body>
</html>


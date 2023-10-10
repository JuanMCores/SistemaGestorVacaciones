<?php
require 'conexionbd.php';
require 'verificar_sesion.php';

// Variable para almacenar el término de búsqueda
$estadoBuscado = '';

// Verificar si se ha enviado un término de búsqueda
if (isset($_GET['estado'])) {
    $estadoBuscado = $_GET['estado'];
}

// Consulta SQL para seleccionar solicitudes de vacaciones filtradas por estado si se especifica un término de búsqueda
$query = "SELECT sv.id, sv.empleado_id, sv.days, sv.fecha_inicio, sv.fecha_end, sv.estado, e.name, e.lastname, e.dni FROM solicitudes_vacaciones sv
          LEFT JOIN empleados e ON sv.empleado_id = e.id";

// Agregar la cláusula WHERE para filtrar por estado si se especifica un término de búsqueda
if (!empty($estadoBuscado)) {
    $query .= " WHERE sv.estado LIKE '%$estadoBuscado%'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mostrardatos.css">
    <link rel="stylesheet" href="css/navbar.css">

    <style>
        body {
            background-image: url("img/minimal-wallpaper.png");
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

        /* Estilo para el estado "Pendiente" */
        .estado-pendiente {
            background-color: #ffc107; /* Amarillo */
        }

        /* Estilo para el estado "Aprobado" */
        .estado-aprobado {
            background-color: #28a745; /* Verde */
        }

        /* Estilo para el estado "Rechazado" */
        .estado-rechazado {
            background-color: #dc3545; /* Rojo */
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
        <a href="mostrardatos.php">Datos Empleados</a>
        <a href="index.php">Cerrar Sesión</a>
    </nav>
</header>

<div class="container mt-5">
    <h2>Solicitudes de Vacaciones</h2>

    <div class="float-right mb-3">
        <form method="get" action="">
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
        echo "<th>Estado</th>";
        echo "<th>Acciones</th>";
        echo "<th>Aprobar</th>";
        echo "<th>Rechazar</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
        // Define el color de fondo según el estado
            $backgroundColor = '';
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
                    $backgroundColor = '';
            }
            echo "<tr id='tablaVacaciones'>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["lastname"] . "</td>";
            echo "<td>" . $row["dni"] . "</td>";
            echo "<td>" . $row["days"] . "</td>";
            echo "<td>" . $row["fecha_inicio"] . "</td>";
            echo "<td>" . $row["fecha_end"] . "</td>";
            // Aplica el color de fondo
            echo "<td id='estado-" . $row["id"] . "' style='background-color: $backgroundColor; color: $textColor;'>" . $row["estado"] . "</td>";
            echo "<td>
                <a href='editarvacaciones.php?id=" . $row["id"] . "'><ion-icon name='create' class='btn-modificar'></ion-icon></a> 
                <a href='javascript:;' onclick='confirmarEliminarVacacion(" . $row["id"] . ")'><ion-icon name='close-circle' class='btn-eliminar'></ion-icon></a>
                </td>";
            echo "<td>    
                <button onclick='aprobarVacacion(" . $row["id"] . ")' class='btn btn-primary'>Aprobar</button>
                </td>";
            echo "<td>    
                <button onclick='rechazarVacacion(" . $row["id"] . ")' class='btn btn-primary'>Rechazar</button>
                </td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        } else {
        echo "<div class='alert alert-warning' role='alert'>";
        echo "No hay solicitudes de vacaciones en este momento. Si tienes algún problema, comunícate con algún administrador.";
        echo "</div>";
    }
    ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
<script src="js\bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script> 
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    function confirmarEliminarVacacion(vacacionId) {
        // Utiliza SweetAlert2 para mostrar un cuadro de diálogo de confirmación
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la solicitud de vacaciones. ¿Deseas continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, llama a la función para eliminar la solicitud
                eliminarVacacion(vacacionId);
            }
        });
    }

    function eliminarVacacion(vacacionId) {
        // Utiliza AJAX para eliminar la solicitud de vacaciones
        $.ajax({
            type: "GET",  // Cambia a GET
            url: "eliminarVacaciones.php?id=" + vacacionId,  // Envía la ID en la URL
            success: function(response) {
                if (response === "success") {
                    // Eliminación exitosa
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'La Solicitud de vacaciones fue eliminada correctamente.',
                        icon: 'success'
                    }).then((result) => {
                        // Recarga la página actual para reflejar los cambios
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al eliminar la solicitud de vacaciones.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    function aprobarVacacion(vacacionId) {
        // Supongamos que la solicitud se aprobó con éxito y el estado ahora es "Aprobado"
        var nuevoEstado = "Aprobado";
        actualizarEstado(vacacionId, nuevoEstado);
    }

    function rechazarVacacion(vacacionId) {
        // Supongamos que la solicitud se rechazó y el estado ahora es "Rechazado"
        var nuevoEstado = "Rechazado";
        actualizarEstado(vacacionId, nuevoEstado);
    }

    function actualizarEstado(vacacionId, nuevoEstado) {
        // Aquí puedes escribir el código para actualizar el estado en la base de datos utilizando AJAX
        // Debes enviar una solicitud al servidor para actualizar el estado de la solicitud de vacaciones con el nuevoEstado.
        
        // Ejemplo de código AJAX (Debes adaptarlo a tu entorno y base de datos):
        
        $.ajax({
            type: "POST",
            url: "actualizar_estado.php", // Archivo PHP para actualizar el estado en la base de datos
            data: {
                id: vacacionId,
                estado: nuevoEstado
            },
            success: function(response) {
                if (response === "success") {
                    // Actualización exitosa
                    Swal.fire({
                        title: 'Actualizado',
                        text: 'Solicitud de vacaciones ' + nuevoEstado + ' para ID: ' + vacacionId,
                        icon: 'success'
                    }).then((result) => {
                        // Actualiza el texto del estado en la tabla
                        var estadoElement = document.getElementById("estado-" + vacacionId);
                        if (estadoElement) {
                            estadoElement.textContent = nuevoEstado;
                        }
                        // No redirigir, solo actualizar la página actual
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al actualizar el estado.',
                        icon: 'error'
                    });
                }
            }
        });
    }
</script>
</body>
</html>

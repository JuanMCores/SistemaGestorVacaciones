<?php
require 'conexionbd.php';
require 'verificar_sesion.php';

$userId = $_SESSION["id"];

// Consulta SQL para seleccionar los datos del empleado en sesión
$query = "SELECT * FROM empleados WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontraron datos del empleado en sesión
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "No se encontraron datos para este usuario.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Datos</title>
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/mostrardatos.css">
    <link rel="stylesheet" href="css/navbar.css">

    <style>
        body {
            background-color: #f7f7f7;
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
    if (isset($_SESSION["id"])) {
        echo ("Bienvenido ".$row["name"]); // Mostrar el nombre del empleado en sesión
    } else {
        echo "Usuario Desconocido";
    }
    ?>
    </h2>
    <nav class="navigation">
        <a href="solicitudesVacaciones_empleados.php?id=<?php echo $userId; ?>">Ver mis Vacaciones</a>
        <a href="index.php">Cerrar Sesión</a>
    </nav>
</header>

<div class="container mt-5">
    <h2>Mis Datos</h2>  
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>
                <th>Años en la Empresa</th>
                <th>Teléfono</th>
                <th>Nacionalidad</th>
                <th>Localidad</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                echo "<tr class='data-row' data-id='" . $row["id"] . "'>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["lastname"] . "</td>";
                echo "<td>" . $row["dni"] . "</td>";
                echo "<td>" . $row["anios_ingreso"] . "</td>";
                echo "<td>" . $row["telefono"] . "</td>";
                echo "<td>" . $row["nacionalidad"] . "</td>";
                echo "<td>" . $row["localidad"] . "</td>";
                echo "</tr>";
            } else {
                echo "No se encontraron datos para este usuario.";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="js\bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
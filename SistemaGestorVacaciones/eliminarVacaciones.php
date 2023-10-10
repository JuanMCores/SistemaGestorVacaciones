<?php
require 'conexionbd.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Consulta SQL para eliminar la solicitud de vacaciones con el ID especificado
    $deleteVacationQuery = "DELETE FROM solicitudes_vacaciones WHERE id=?";
    $deleteVacationStmt = $conn->prepare($deleteVacationQuery);
    $deleteVacationStmt->bind_param("i", $id);

    // Ejecutar la eliminación
    if ($deleteVacationStmt->execute()) {
        echo "success"; // Envía una respuesta exitosa
    } else {
        echo "error"; // Envía una respuesta de error
    }
} else {
    echo "ID no especificado.";
}

$conn->close();
?>

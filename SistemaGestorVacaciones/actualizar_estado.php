<?php
require 'conexionbd.php';

// Verificar si se ha proporcionado un ID de solicitud de vacaciones y un nuevo estado
if (isset($_POST["id"]) && isset($_POST["estado"])) {
    $vacacionId = $_POST["id"];
    $nuevoEstado = $_POST["estado"];

    // Consulta SQL para actualizar el estado de la solicitud de vacaciones
    $query = "UPDATE solicitudes_vacaciones SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nuevoEstado, $vacacionId);

    if ($stmt->execute()) {
        // La actualización fue exitosa
        echo "success";
    } else {
        // Hubo un error en la actualización
        echo "error";
    }
} else {
    // Si no se proporcionan los parámetros esperados, muestra un mensaje de error
    echo "error";
}
?>

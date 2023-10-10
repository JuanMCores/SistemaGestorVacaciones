<?php
require 'conexionbd.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Iniciar una transacción para asegurar la consistencia de los datos
    $conn->begin_transaction();

    try {
        // Consulta SQL para eliminar todas las solicitudes de vacaciones del empleado con el ID especificado
        $deleteVacationsQuery = "DELETE FROM solicitudes_vacaciones WHERE empleado_id=?";
        $deleteVacationsStmt = $conn->prepare($deleteVacationsQuery);
        $deleteVacationsStmt->bind_param("i", $id);

        // Consulta SQL para eliminar el registro de login_register relacionado con el ID de empleado
        $deleteLoginQuery = "DELETE FROM login_register WHERE id=?";
        $deleteLoginStmt = $conn->prepare($deleteLoginQuery);
        $deleteLoginStmt->bind_param("i", $id);

        // Consulta SQL para eliminar el registro de empleados con el ID especificado
        $deleteEmployeeQuery = "DELETE FROM empleados WHERE id=?";
        $deleteEmployeeStmt = $conn->prepare($deleteEmployeeQuery);
        $deleteEmployeeStmt->bind_param("i", $id);

        // Ejecuta la eliminación de las solicitudes de vacaciones
        if ($deleteVacationsStmt->execute()) {
            // Ejecuta la eliminación del registro de login_register
            if ($deleteLoginStmt->execute()) {
                // Ejecuta la eliminación del empleado
                if ($deleteEmployeeStmt->execute()) {
                    // Confirma la transacción si todo fue exitoso
                    $conn->commit();
                    header("Location: mostrardatos.php?message=success"); // Redirige con un mensaje de éxito
                    exit;
                } else {
                    throw new Exception("Error al eliminar el registro de empleado.");
                }
            } else {
                throw new Exception("Error al eliminar el registro de login_register.");
            }
        } else {
            throw new Exception("Error al eliminar las solicitudes de vacaciones.");
        }
    } catch (Exception $e) {
        // Manejo de excepciones en caso de error
        $conn->rollback();
        header("Location: mostrardatos.php?message=error"); // Redirige con un mensaje de error
        exit;
    }
} else {
    echo "ID no especificado.";
}

$conn->close();
?>

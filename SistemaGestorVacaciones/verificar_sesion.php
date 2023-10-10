<?php
// Inicia la sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["login"])) {
    echo "<script>alert('Debes iniciar sesión primero.'); window.location.href = 'index.php';</script>";
    exit;
}
?>





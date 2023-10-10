<?php
require 'conexionbd.php';

if (isset($_POST["submit"])) {
    // Verificar si el captcha reCAPTCHA ha sido resuelto correctamente
    $ip = $_SERVER['REMOTE_ADDR'];
    $captcha = $_POST['g-recaptcha-response'];
    $secretkey = "6Ld1em8oAAAAAMbUzbmJHtzwIKMyQkYvqitwdjns";

    $respuesta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");

    $atributos = json_decode($respuesta, TRUE);

    if (!$atributos['success']) {
        // El captcha no se completó correctamente, muestra un mensaje de error.
        echo "<script> alert('Por favor, completa el captcha correctamente.'); </script>";
    } else {
        // El captcha se completó correctamente, procede con la verificación de credenciales.

        $usernameemail = $_POST["usernameemail"];
        $password = $_POST["password"]; // Contraseña en texto plano

        $errors = array();

        // Contraseña encriptada para "admin"
        $adminPasswordHash = password_hash("admin", PASSWORD_DEFAULT);

        // Verificar si el usuario es el administrador
        if ($usernameemail === "admin" && password_verify($password, $adminPasswordHash)) {
            $_SESSION["login"] = true;
            $_SESSION["role"] = "admin"; // Agregar un rol para el administrador si es necesario
            header("Location: mostrardatos.php");
            exit;
        }

        // Consulta SQL para buscar al usuario por nombre de usuario o correo electrónico
        $query = "SELECT * FROM login_register WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $usernameemail, $usernameemail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verificar si la contraseña ingresada coincide con la almacenada (ambas encriptadas)
            if (password_verify($password, $row["password"])) {
                // Autenticación exitosa, ahora obtenemos la ID del empleado
                $employeeResult = mysqli_query($conn, "SELECT id FROM empleados WHERE id = " . $row["id"]);
                $employeeRow = mysqli_fetch_assoc($employeeResult);

                if ($employeeRow) {
                    $_SESSION["login"] = true;
                    $_SESSION["id"] = $employeeRow["id"]; // Guarda la ID del empleado en la sesión
                    $userRole = $row["role"];

                    // Verificar el rol del usuario y redirigir en consecuencia
                    if ($userRole === "admin") {
                        header("Location: mostrardatos.php");
                        exit;
                    } elseif ($userRole === "empleado") {
                        header("Location: solicitudesVacaciones_empleados.php?id=" . $_SESSION["id"]); // Redirigir a mostrardatos_empleados.php con la ID del empleado
                        exit;
                    } else {
                        exit;
                    }
                } else {
                    echo "<script> alert('Error: No se encontró el empleado correspondiente, la ID del usuario registrador no coincide con la ID del EMPLEADO REGISTRADO.'); </script>";
                }
            } else {
                echo "<script> alert('Contraseña Incorrecta.'); </script>";
            }
        } else {
            echo "<script> alert('La cuenta no se encuentra registrada.'); </script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title> 
    <link href="css\bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="css/inicio_sesion.css">
</head>
<body style="background: url('img/fondo.png');">
    <div style="opacity: 0.85; background-color:white;" class="wrapper">
        <div class="form-box register">
            <h2>Iniciar Sesión</h2>
            <form class="" action="" method = "post" autocomplete="off">
                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="person-circle">
                    </ion-icon></span>
                    <input type="text" name="usernameemail" id="usernameemail" required value=""> 
                    <label for="usernameemail">Usuario o Email</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <span class="password-toggle" id="password-toggle">
                            <ion-icon name="eye"></ion-icon>
                        </span>
                        <ion-icon name="lock-closed"></ion-icon>     
                    </span>
                    <input type="password" minlength="4" maxlength="16" name="password" id="password" required value=""> 
                    <label for="password">Contraseña</label>
                </div>     

                <div class="g-recaptcha" data-sitekey="6Ld1em8oAAAAALxP0CdlfoFaUZpT3rpT0OCW6cUx">
                </div><br>

                <button class="btn" type="submit" name="submit">Login</button>

                <!-- <div class="login-register">
                    <p>¿Aún no tienes una cuenta creada?
                    <a href="registration.php">Registration</a></p>
                </div> -->
            </form>
        </div>
    </div>
    <script src="js\bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script type ="module" src ="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script> 
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script> 
        // Para inpeccionar la contraseña
        const passwordInput = document.getElementById("password");
        const passwordToggle = document.getElementById("password-toggle");

        passwordToggle.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.innerHTML = '<ion-icon name="eye-off"></ion-icon>';
            } else {
                passwordInput.type = "password";
                passwordToggle.innerHTML = '<ion-icon name="eye"></ion-icon>';
            }
        });
    </script>
</body>
</html>
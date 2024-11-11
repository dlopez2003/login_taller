<?php
session_start(); // Iniciar la sesión

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "formulario");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    // Comprobar si el correo ya existe
    $sql_check = "SELECT * FROM usuarios WHERE email='$email'";
    $result_check = $conn->query($sql_check);
    
    if ($result_check->num_rows == 0) {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, email, contraseña) VALUES ('$nombre', '$email', SHA2('$contraseña', 256))";
        
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Usuario creado exitosamente. Puedes iniciar sesión.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>El correo ya está registrado.</div>";
    }
}

// Procesar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    // Preparar la consulta
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND contraseña = SHA2('$contraseña', 256)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // El usuario existe, iniciar sesión
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        header("Location: bienvenido.php"); // Redirigir a la página de bienvenida
        exit();
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro e Inicio de Sesión - Blade Runner 2049</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0') no-repeat center center fixed;
            background-size: cover;
            color: #e0e0e0;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 500px;
            margin-top: 100px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .btn-primary {
            width: 100%;
            background-color: #00bcd4;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0097a7;
        }
        .alert {
            margin-top: 20px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .login-form {
            display: none; /* Ocultar el formulario de inicio de sesión inicialmente */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary" name="register">Crear Usuario</button>
        </form>

        <div class="mt-4 text-center">
            <button class="btn btn-secondary" id="toggleLogin">Iniciar Sesión</button>
        </div>

        <div class="login-form mt-5">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="login_email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="login_email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="login_contraseña" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="login_contraseña" name="contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary" name="login">Iniciar Sesión</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar u ocultar los formularios
        document.getElementById('toggleLogin').onclick = function() {
            const loginForm = document.querySelector('.login-form');
            const registerForm = document.querySelector('form:first-of-type'); // El primer formulario (registro)

            if (loginForm.style.display === 'none' || loginForm.style.display === '') {
                // Si el formulario de inicio de sesión está oculto, mostrarlo
                loginForm.style.display = 'block';
                registerForm.style.display = 'none'; // Ocultar el formulario de registro
            } else {
                // Si el formulario de inicio de sesión está visible, ocultarlo
                loginForm.style.display = 'none';
                registerForm.style.display = 'block'; // Mostrar el formulario de registro
            }
        };
    </script>
</body>
</html>

<?php
session_start();
require 'db.php'; // Incluye el archivo de conexión a la base de datos

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Consulta para obtener todos los usuarios
$stmt = $pdo->prepare("SELECT * FROM usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Edición de datos
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        
        $updateStmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
        $updateStmt->execute([$nombre, $email, $id]);
        header("Location: bienvenido.php");
        exit();
    }

    // Eliminación de datos
    if (isset($_POST['eliminar'])) {
        $id = $_POST['id'];
        
        $deleteStmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $deleteStmt->execute([$id]);
        header("Location: bienvenido.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <style>
        /* Estilos personalizados */
        body { ... } /* Resto de estilos */
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?>!</h1>
        <p>Has iniciado sesión exitosamente.</p>

        <h2>Lista de Usuarios</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <form method="POST">
                        <td><?php echo $usuario['id']; ?></td>
                        <td><input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" class="form-control" /></td>
                        <td><input type="email" name="email" value="<?php echo $usuario['email']; ?>" class="form-control" /></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>" />
                            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
                            <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="logout.php" class="btn btn-danger mt-3">Cerrar Sesión</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

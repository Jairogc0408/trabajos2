<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Eliminar producto
if (isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$productId]);
    header("Location: admin.php"); // Redirige para refrescar la lista de productos
    exit;
}

// Eliminar usuario
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    header("Location: admin.php"); // Redirige para refrescar la lista de usuarios
    exit;
}

// Mostrar productos
$stmt = $conn->query("SELECT * FROM productos");
$productos = $stmt->fetchAll();

// Mostrar usuarios
$stmt = $conn->query("SELECT * FROM users");
$usuarios = $stmt->fetchAll();

// Agregar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'], $_POST['precio'], $_POST['stock'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $precio, $stock]);
    header("Location: admin.php"); // Refresca la lista de productos
    exit;
}

// Agregar usuario
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    header("Location: admin.php"); // Refresca la lista de usuarios
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/cetis.css"> <!-- Incluye el archivo CSS -->
</head>
<body>
    <h1>Panel de Administración</h1>

    <h2>Agregar Producto</h2>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="precio">Precio:</label>
        <input type="number" step="0.01" name="precio" id="precio" required>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" required>

        <button type="submit">Agregar</button>
    </form>

    <h2>Productos Existentes</h2>
    <table>
        <tr><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acción</th></tr>
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                <td>$<?php echo htmlspecialchars($producto['precio']); ?></td>
                <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                <td>
                    <!-- Botón para borrar producto -->
                    <a href="admin.php?delete=<?php echo $producto['id']; ?>" 
                       onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');"
                       class="delete-button">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Gestionar Usuarios</h2>
    <form method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <label for="role">Rol:</label>
        <select name="role" id="role">
            <option value="user">Usuario</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit" name="add_user">Agregar Usuario</button>
    </form>

    <h2>Usuarios Existentes</h2>
    <table>
        <tr><th>Nombre de Usuario</th><th>Rol</th><th>Acción</th></tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                <td><?php echo htmlspecialchars($usuario['role']); ?></td>
                <td>
                    <!-- Botón para borrar usuario -->
                    <a href="admin.php?delete_user=<?php echo $usuario['id']; ?>" 
                       onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"
                       class="delete-button">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>

<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

// Mostrar productos
$stmt = $conn->query("SELECT * FROM productos WHERE stock > 0");
$productos = $stmt->fetchAll();

// Realizar pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $user_id = $_SESSION['user_id'];

    // Verificar si hay suficiente stock
    $stmt = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if ($producto && $producto['stock'] >= $cantidad) {
        // Insertar el pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (user_id, producto_id, cantidad) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $producto_id, $cantidad]);

        // Actualizar stock del producto
        $nuevo_stock = $producto['stock'] - $cantidad;
        $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->execute([$nuevo_stock, $producto_id]);

        echo "<p class='success'>Pedido realizado exitosamente.</p>";
    } else {
        echo "<p class='error'>No hay suficiente stock para este pedido.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacer Pedido</title>
    <link rel="stylesheet" href="css/cetis.css">
</head>
<body>
    <h1>Bienvenido a la Papelería</h1>

    <h2>Realizar Pedido</h2>
    <form method="POST" action="user.php">
        <label for="producto_id">Producto:</label>
        <select name="producto_id" id="producto_id" required>
            <?php foreach ($productos as $producto): ?>
                <option value="<?php echo $producto['id']; ?>">
                    <?php echo htmlspecialchars($producto['nombre']); ?> - $<?php echo htmlspecialchars($producto['precio']); ?> (Stock: <?php echo htmlspecialchars($producto['stock']); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" min="1" required>

        <button type="submit">Hacer Pedido</button>
    </form>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>

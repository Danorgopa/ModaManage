<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.html");
  exit;
}

$conn = new mysqli("localhost", "root", "", "empresa_inventario"); // Conexión con la base de datos

// Verificar si se está actualizando un producto
if (isset($_POST['edit_id'])) {
  $id = $_POST['edit_id'];
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $cantidad = $_POST['cantidad'];
  $precio = $_POST['precio'];

  // Escapar los valores para evitar inyecciones SQL
  $id = $conn->real_escape_string($id);
  $nombre = $conn->real_escape_string($nombre);
  $descripcion = $conn->real_escape_string($descripcion);
  $cantidad = $conn->real_escape_string($cantidad);
  $precio = $conn->real_escape_string($precio);

  // Actualizar los datos del producto en la tabla inventario
  $conn->query("UPDATE inventario SET nombre_producto='$nombre', descripcion='$descripcion', cantidad='$cantidad', precio_unitario='$precio' WHERE id = $id");

  // Redirigir a inventario.php después de guardar los cambios
  header("Location: inventario.php");
  exit;
}

// Obtener datos del producto a editar
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM inventario WHERE id = $id");
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Producto</title>
  <link rel="stylesheet" href="css/edit_device3.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
</head>

<body>
  <div class="edit-device-container">
    <h2>Editar Producto</h2>
    <form method="POST" action="edit_product.php?id=<?php echo $row['id']; ?>"> <!-- Mantener la acción en la misma página -->
      <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">

      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre_producto']); ?>" required><br><br>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea><br><br>

      <label for="cantidad">Cantidad:</label>
      <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" required><br><br>

      <label for="precio">Precio:</label>
      <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($row['precio_unitario']); ?>" required><br><br>

      <input type="submit" value="Guardar Cambios">
      <button type="button" onclick="location.href='inventario.php'">Regresar</button> <!-- Botón para regresar -->
    </form>
  </div>
</body>

</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>

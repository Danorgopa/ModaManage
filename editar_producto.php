<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.html");
  exit;
}
// Configuración de la conexión a la base de datos
$host = "sql306.infinityfree.com"; // Cambia según tu configuración
$user = "if0_37701389"; // Cambia según tu configuración
$password = "mvOCfmjvomz"; // Cambia según tu configuración
$database = "if0_37701389_empresa_inventario";

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['edit_id'];
  $nombre_producto = $_POST['nombre_producto'];
  $descripcion = $_POST['descripcion'];
  $cantidad = $_POST['cantidad'];
  $precio_unitario = $_POST['precio_unitario'];

  $stmt = $conn->prepare("UPDATE inventario SET nombre_producto = ?, descripcion = ?, cantidad = ?, precio_unitario = ? WHERE id = ?");
  $stmt->bind_param("ssidi", $nombre_producto, $descripcion, $cantidad, $precio_unitario, $id);

  if ($stmt->execute()) {
    // Mostrar alerta usando JavaScript
    echo "<script>
            alert('Producto actualizado con éxito.');
            window.location.href = 'inventario.php'; // Redireccionar después de la alerta
          </script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
  exit;
}


// Obtener datos del dispositivo a editar
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
    <form method="POST" action=""> <!-- Formulario POST que se procesa en esta misma página -->
      <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($row['id']); ?>">

      <label for="nombre_producto">Nombre:</label>
      <input type="text" id="nombre_producto" name="nombre_producto" value="<?php echo htmlspecialchars($row['nombre_producto']); ?>" required><br><br>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea><br><br>

      <label for="cantidad">Cantidad:</label>
      <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" required><br><br>

      <label for="precio_unitario">Precio:</label>
      <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" value="<?php echo htmlspecialchars($row['precio_unitario']); ?>" required><br><br>

      <input type="submit" value="Guardar Cambios">
      <button type="button" onclick="location.href='inventario.php'">Regresar</button> <!-- Botón para regresar -->
    </form>
  </div>
</body>

</html>
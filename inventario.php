<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.html");
  exit;
}

$conn = new mysqli("localhost", "root", "", "empresa_inventario"); // Conexión con la base de datos

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se está eliminando un producto
if (isset($_GET['delete_id'])) {
  $delete_id = $_GET['delete_id'];

  // Escapar el ID para evitar inyecciones SQL
  $delete_id = $conn->real_escape_string($delete_id);

  // Eliminar el producto de la tabla inventario
  $conn->query("DELETE FROM inventario WHERE id = $delete_id");

  // Redireccionar después de eliminar
  header("Location: inventario.php");
  exit;
}

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

  // Actualizar la información del producto
  $conn->query("UPDATE inventario SET nombre_producto='$nombre', descripcion='$descripcion', cantidad='$cantidad', precio_unitario='$precio' WHERE id = $id");

  // Redireccionar después de la actualización
  header("Location: inventario.php");
  exit;
}

// Consulta para obtener los productos en el inventario
$result = $conn->query("SELECT * FROM inventario");

// Verificar si la consulta fue exitosa
if (!$result) {
  die("Error en la consulta: " . $conn->error);
}

$totalPrecio = 0; // Variable para almacenar el precio total
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Inventario</title>
  <link rel="stylesheet" href="css/inventario1.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>

<body>
  <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <header class="topbar">
        <h1>Gestión de Inventario</h1>
        <div class="actions">
          <a href="nuevo_producto.php" class="btn">Agregar</a>
          <a href="logout.php" class="btn">Salir</a>
        </div>
      </header>
      <div class="content">
        <table class="doc-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Precio (UNI)</th>
              <th>Precio Total</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()):
                $precioTotal = $row['precio_unitario'] * $row['cantidad']; // Calcular precio total por producto
                $totalPrecio += $precioTotal; // Calcular precio total acumulado
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                  <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                  <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                  <td><?php echo htmlspecialchars($row['precio_unitario']); ?> COP</td>
                  <td><?php echo number_format($precioTotal, 2); ?> COP</td>
                  <td>
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-btn">Editar</a> |
                    <a href="inventario.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar este producto?');">Eliminar</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No hay productos registrados.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <h2>Precio Total General: <?php echo number_format($totalPrecio, 2); ?> COP</h2>
      </div>
    </div>
  </div>

  <script>
    // Aquí puedes agregar cualquier script adicional si es necesario
  </script>
</body>

</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
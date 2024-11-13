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

// Verificar si se está actualizando el estado de un proveedor (en lugar de eliminar)
if (isset($_GET['delete_id'])) {
  $delete_id = $_GET['delete_id'];

  // Escapar el ID para evitar inyecciones SQL
  $delete_id = $conn->real_escape_string($delete_id);

  // Actualizar el estado del proveedor a 0 (inactivo)
  $conn->query("UPDATE proveedor SET estado = 0 WHERE id = $delete_id");

  // Redireccionar después de actualizar el estado
  header("Location: proveedores.php");
  exit;
}

// Verificar si se está actualizando un proveedor
if (isset($_POST['edit_id'])) {
  $id = $_POST['edit_id'];
  $nombre = $_POST['nombre'];
  $contacto = $_POST['contacto'];
  $telefono = $_POST['telefono'];
  $direccion = $_POST['direccion'];
  $correo = $_POST['correo'];

  // Escapar los valores para evitar inyecciones SQL
  $id = $conn->real_escape_string($id);
  $nombre = $conn->real_escape_string($nombre);
  $contacto = $conn->real_escape_string($contacto);
  $telefono = $conn->real_escape_string($telefono);
  $direccion = $conn->real_escape_string($direccion);
  $correo = $conn->real_escape_string($correo);

  // Actualizar la información del proveedor
  $conn->query("UPDATE proveedor SET nombre='$nombre', contacto='$contacto', telefono='$telefono', direccion='$direccion', correo='$correo' WHERE id = $id");

  // Redireccionar después de la actualización
  header("Location: proveedores.php");
  exit;
}

// Consulta para obtener los proveedores activos (estado = 1)
$result = $conn->query("SELECT * FROM proveedor WHERE estado = 1");

// Verificar si la consulta fue exitosa
if (!$result) {
  die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Proveedores</title>
  <link rel="stylesheet" href="css/inventario1.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>

<body>
  <div class="dashboard-container">
    <?php
    // Verificar si el rol del usuario es 1 (admin) y cargar el sidebar correspondiente
    if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
      include 'sidebar.php'; // Sidebar para admin
    } else {
      include 'sidebaruser.php'; // Sidebar para usuarios normales
    }
    ?>
    <div class="main-content">
      <header class="topbar">
        <h1>Gestión de Proveedores</h1>
        <div class="actions">
          <a href="crear_proovedor.php" class="btn">Agregar</a>
          <a href="logout.php" class="btn">Salir</a>
        </div>
      </header>
      <div class="content">
        <table class="doc-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Contacto</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Correo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                  <td><?php echo htmlspecialchars($row['contacto']); ?></td>
                  <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                  <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                  <td><?php echo htmlspecialchars($row['correo']); ?></td>
                  <td>
                    <a href="editar_proovedor.php?id=<?php echo $row['id']; ?>" class="edit-btn">Editar</a> |
                    <a href="proveedores.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar este proveedor?');">Eliminar</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No hay proveedores registrados.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
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
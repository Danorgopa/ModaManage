<?php
// Configuración de la conexión a la base de datos
$host = "localhost"; // Cambia según tu configuración
$user = "root"; // Cambia según tu configuración
$password = ""; // Cambia según tu configuración
$database = "empresa_inventario";

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Obtener los datos del proveedor si se proporciona un ID
$id = "";
$proveedor = null;
if (isset($_GET['id'])) {
  $id = intval($_GET['id']); // Convertir a entero para seguridad

  // Consulta para obtener los datos del proveedor
  $sql = "SELECT * FROM proveedor WHERE id = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
  }

  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $proveedor = $result->fetch_assoc();
  $stmt->close();
}

// Comprobar si se han enviado los datos del formulario para actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger datos del formulario
  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $contacto = $_POST['contacto'];
  $telefono = $_POST['telefono'];
  $direccion = $_POST['direccion'];
  $correo = $_POST['correo'];
  $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

  // Actualizar los datos del proveedor
  $stmt = $conn->prepare("UPDATE proveedor SET nombre = ?, contacto = ?, telefono = ?, direccion = ?, correo = ?, estado = ? WHERE id = ?");

  if ($stmt === false) {
    die("Error preparando la declaración: " . $conn->error);
  }

  // Vincular parámetros
  $stmt->bind_param(
    "ssssssi",
    $nombre,
    $contacto,
    $telefono,
    $direccion,
    $correo,
    $estado,
    $id
  );

  // Ejecutar la declaración
  if ($stmt->execute()) {
    // Mostrar mensaje de éxito y redirigir a proveedores.php
    echo "<script>
            alert('Proveedor actualizado correctamente.');
            window.location.href = 'proveedores.php';
          </script>";
  } else {
    die("Error al ejecutar la declaración: " . $stmt->error);
  }

  // Cerrar la declaración
  $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Proveedor</title>
  <link rel="stylesheet" href="css/nuevos_dps2.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
</head>

<body>
  <div class="overlay"></div>
  <div class="container">
    <h2>Editar Proveedor</h2>
    <form action="" method="post">
      <input type="hidden" name="id" value="<?php echo isset($proveedor['id']) ? htmlspecialchars($proveedor['id']) : ''; ?>">

      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" value="<?php echo isset($proveedor['nombre']) ? htmlspecialchars($proveedor['nombre']) : ''; ?>" required><br>

      <label for="contacto">Contacto:</label>
      <input type="text" name="contacto" value="<?php echo isset($proveedor['contacto']) ? htmlspecialchars($proveedor['contacto']) : ''; ?>" required><br>

      <label for="telefono">Teléfono:</label>
      <input type="text" name="telefono" value="<?php echo isset($proveedor['telefono']) ? htmlspecialchars($proveedor['telefono']) : ''; ?>"><br>

      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion" value="<?php echo isset($proveedor['direccion']) ? htmlspecialchars($proveedor['direccion']) : ''; ?>"><br>

      <label for="correo">Correo:</label>
      <input type="email" name="correo" value="<?php echo isset($proveedor['correo']) ? htmlspecialchars($proveedor['correo']) : ''; ?>"><br>

      <label for="estado">Estado:</label>
      <select name="estado">
        <option value="1" <?php echo (isset($proveedor['estado']) && $proveedor['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
        <option value="0" <?php echo (isset($proveedor['estado']) && $proveedor['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
      </select><br>

      <input type="submit" value="Actualizar Proveedor">
      <button type="button" class="btn" onclick="window.location.href='proveedores.php';">Regresar</button>
    </form>
  </div>
</body>

</html>
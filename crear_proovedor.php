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

// Comprobar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger datos del formulario
  $nombre = $_POST['nombre'];
  $contacto = $_POST['contacto'];
  $telefono = $_POST['telefono'];
  $direccion = $_POST['direccion'];
  $correo = $_POST['correo'];
  $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1; // Valor por defecto 1 (activo)

  // Preparar la consulta SQL
  $stmt = $conn->prepare("INSERT INTO proveedor (nombre, contacto, telefono, direccion, correo, estado) VALUES (?, ?, ?, ?, ?, ?)");

  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }

  // Vincular parámetros
  $stmt->bind_param(
    "sssssi",
    $nombre,
    $contacto,
    $telefono,
    $direccion,
    $correo,
    $estado
  );

  // Ejecutar la declaración
  if ($stmt->execute()) {
    // Mostrar mensaje de éxito y redirigir a la página de proveedores
    echo "<script>
            alert('Proveedor creado correctamente.');
            window.location.href = 'proveedores.php';
          </script>";
  } else {
    die("Error executing statement: " . $stmt->error);
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
  <title>Añadir Nuevo Proveedor</title>
  <link rel="stylesheet" href="css/nuevos_dps2.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
</head>

<body>
  <div class="overlay"></div>
  <div class="container">
    <h2>Añadir Nuevo Proveedor</h2>
    <form action="" method="post">
      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" required><br>

      <label for="contacto">Contacto:</label>
      <input type="text" name="contacto" required><br>

      <label for="telefono">Teléfono:</label>
      <input type="text" name="telefono"><br>

      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion"><br>

      <label for="correo">Correo:</label>
      <input type="email" name="correo"><br>

      <label for="estado">Estado:</label>
      <select name="estado">
        <option value="1" selected>Activo</option>
        <option value="0">Inactivo</option>
      </select><br>

      <input type="submit" value="Añadir Proveedor">
      <button type="button" class="btn" onclick="window.location.href='proveedores.php';">Regresar</button>
    </form>
  </div>
</body>

</html>
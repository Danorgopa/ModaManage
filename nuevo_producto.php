<?php
session_start(); // Iniciar sesión para almacenar mensajes

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'empresa_inventario');

// Comprobar la conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Obtener los datos del formulario
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $cantidad = $_POST['cantidad'];
  $precio = $_POST['precio'];


  // Validar que los campos obligatorios no estén vacíos
  if (empty($nombre) || empty($descripcion) || empty($cantidad) || empty($precio)) {
    $_SESSION['message'] = "Por favor, completa todos los campos obligatorios.";
    header('Location: mensaje.php');
    exit();
  }

  // Preparar la consulta de inserción en la tabla inventario
  $sql = $conn->prepare("INSERT INTO inventario (nombre_producto, descripcion, cantidad, precio_unitario) 
                         VALUES (?, ?, ?, ?)");

  // Verificar si la preparación fue exitosa
  if (!$sql) {
    die("Error en la preparación de la consulta: " . $conn->error);
  }

  // Asignar parámetros
  $sql->bind_param('ssid', $nombre, $descripcion, $cantidad, $precio);

  // Ejecutar la consulta
  if ($sql->execute()) {
    $_SESSION['message'] = "Dispositivo añadido exitosamente.";
  } else {
    $_SESSION['message'] = "Error al añadir dispositivo: " . $conn->error;
  }

  // Cerrar la consulta
  $sql->close();

  // Redirigir a la página de mensaje
  header('Location: mensaje.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Añadir Nuevo Dispositivo</title>
  <link rel="stylesheet" href="css/nuevos_dps2.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
</head>

<body>
  <div class="overlay"></div>
  <div class="container">
    <h2>Añadir Nuevo Dispositivo</h2>
    <form action="nuevo_producto.php" method="post">
      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" required><br>

      <label for="descripcion">Descripción:</label>
      <textarea name="descripcion" required></textarea><br>

      <label for="cantidad">Cantidad:</label>
      <input type="number" name="cantidad" required><br>

      <label for="precio">Precio por unidad:</label>
      <input type="number" name="precio" step="0.01" required><br>

      <input type="submit" value="Guardar Dispositivo">
      <button type="button" class="btn" onclick="window.location.href='inventario.php';">Volver</button>
    </form>
  </div>
</body>

</html>

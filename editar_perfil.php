<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
  header("Location: index.html");
  exit;
}

// Conectar a la base de datos
$servername = "sql306.infinityfree.com";
$username = "if0_37701389";
$password = "mvOCfmjvomz"; // tu contraseña
$dbname = "if0_37701389_empresa_inventario";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener información del usuario
$user = $_SESSION['username'];

// Consulta para obtener el ID del usuario basado en el username
$sqlUserId = "SELECT usuario_id FROM login WHERE username = ?";
$stmt = $conn->prepare($sqlUserId);
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->bind_result($usuario_id);
$stmt->fetch();
$stmt->close();

if ($usuario_id) {
  // Consulta para obtener los datos del usuario
  $sql = "SELECT * FROM usuarios WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $userData = $result->fetch_assoc();
  $stmt->close();
} else {
  $userData = null; // Si no se encontró el ID del usuario, se asigna null
}

// Procesar la edición del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  // Obtener la contraseña actual y la nueva
  $password_actual = $_POST['password_actual'] ?? '';
  $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

  // Verificar la contraseña actual si se quiere cambiar
  if (!empty($nueva_contrasena)) {
    // Consulta para verificar la contraseña actual
    $sqlPasswordCheck = "SELECT password FROM login WHERE usuario_id = ?";
    $stmt = $conn->prepare($sqlPasswordCheck);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($password_almacenada);
    $stmt->fetch();
    $stmt->close();

    // Verificar si la contraseña actual es correcta
    if (password_verify($password_actual, $password_almacenada)) {
      // Hashear la nueva contraseña
      $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

      // Consulta para actualizar la información del usuario
      $updateSql = "UPDATE usuarios SET nombre = ?, password = ? WHERE id = ?";
      $updateStmt = $conn->prepare($updateSql);
      $updateStmt->bind_param("ssi", $nombre, $hashed_password, $usuario_id);
    } else {
      $error = "La contraseña actual no es correcta.";
    }
  } else {
    // Consulta para actualizar la información sin cambiar la contraseña
    $updateSql = "UPDATE usuarios SET nombre = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $nombre, $usuario_id);
  }

  // Ejecutar la actualización
  if (isset($updateStmt) && $updateStmt->execute()) {
    header("Location: perfil.php?mensaje=Perfil actualizado con éxito");
    exit;
  } else {
    $error = "Error al actualizar el perfil: " . $conn->error;
  }
}

// Cerrar la conexión solo si fue creada
if (isset($conn)) {
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Perfil - ModaManage</title>
  <link rel="stylesheet" href="css/editar_perfil1.css">

</head>

<body>
  <div class="container">
    <h1>Editar Perfil</h1>
    <?php if (isset($error)): ?>
      <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre']); ?>" required>
      <label for="password_actual">Contraseña Actual:</label>
      <input type="password" id="password_actual" name="password_actual" placeholder="Ingresa tu contraseña actual (si deseas cambiarla)">

      <label for="nueva_contrasena">Nueva Contraseña:</label>
      <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="Ingresa tu nueva contraseña (opcional)">

      <button type="submit">Guardar Cambios</button>
    </form>
    <a href="perfil.php">Volver al Perfil</a>
  </div>
</body>

</html>
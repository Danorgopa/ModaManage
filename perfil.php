<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
  // Si no hay sesión iniciada, redirigir al inicio de sesión
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
  $sql = "SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            JOIN roles r ON u.rol_id = r.id 
            WHERE u.id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $userData = $result->fetch_assoc();
  $stmt->close();
} else {
  $userData = null; // Si no se encontró el ID del usuario, se asigna null
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil - ModaManage</title>
  <link rel="stylesheet" href="css/perfil1.css">
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
        <h1>Perfil de Usuario</h1>
        <div class="actions">
          <a href="editar_perfil.php" class="btn">Editar Perfil</a> <!-- Botón para redirigir a la página de edición -->
          <a href="logout.php" class="btn">Salir</a>
        </div>
      </header>

      <div class="content">
        <?php if ($userData): ?>
          <h2>Bienvenido, <?php echo htmlspecialchars($userData['nombre']); ?></h2>
          <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
          <p><strong>Rol:</strong> <?php echo htmlspecialchars($userData['rol_nombre']); ?></p>
          <p>No se encontraron datos del usuario.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>

</html>
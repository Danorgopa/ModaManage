<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
  // Si no hay sesión iniciada, redirigir al inicio de sesión
  header("Location: index.html");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP</title>
  <link rel="stylesheet" href="css/dashboard1.css">
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

    <!-- Contenido principal -->
    <div class="main-content">
      <!-- Barra superior -->
      <header class="topbar">
        <h1>Panel de Gestión</h1>
        <div class="actions">
          <a href="logout.php" class="btn">Salir</a>
        </div>
      </header>

      <div class="content">
      </div>

      <div class="alert">
        <h2>Alertas de Inventario </h2>

      </div>
    </div>
  </div>
  </header>

  <div class="content">
  </div>

  <div class="alert">
    <h2>Alertas de Inventario </h2>

  </div>
  </div>
  </div>
  </div>
</body>

</html>
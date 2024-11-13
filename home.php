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
  <title>ModaManage</title>
  <link rel="stylesheet" href="css/dashboard1.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>

<body>
  <div class="dashboard-container">
  <?php include 'sidebar.php'; ?>

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
                    <h2>Alertas de Inventario y Mantenimiento</h2>
                    <p>Dispositivos que requieren atención inmediata.</p>
                </div>
            </div>
        </div>
      </header>

      <div class="content">
      </div>

      <div class="alert">
        <h2>Alertas de Inventario y Mantenimiento</h2>
        <p>Dispositivos que requieren atención inmediata.</p>
      </div>
    </div>
  </div>
  </div>
</body>

</html>
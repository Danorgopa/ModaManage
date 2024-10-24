<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // Si no hay sesión iniciada, redirigir al inicio de sesión
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aulapp - Gestión de Inventarios y Mantenimiento</title>
    <link rel="stylesheet" href="../css/dashboard1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>
<body>
    <div class="dashboard-container">
        <!-- Barra lateral -->
        <nav class="sidebar">
            <div class="logo">
                <img src="../img/rosaa-removebg-preview.png" alt="inventory Logo">
            </div>
            <ul>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li> <!-- Enlace al módulo de perfil -->
                <li><a href="inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
                <li><a href="mantenimiento.php"><i class="fas fa-tools"></i> Mantenimiento</a></li>
                <li><a href="generar_reporte.php"><i class="fas fa-chart-line"></i> Reportes</a></li>
                <li><a href="configuracion.php"><i class="fas fa-cog"></i> Informacion</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>

        <!-- Contenido principal -->
        <div class="main-content">
            <!-- Barra superior -->
            <header class="topbar">
                <h1>Panel de Gestión</h1>
                <div class="actions">
                    <a href="nuevo_dispositivo.php" class="btn">Nuevo Dispositivo</a>
                    <a href="generar_reporte.php" class="btn">Generar Reporte</a>
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
    </div>
</body>
</html>
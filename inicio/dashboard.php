<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aulapp - Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>  <!-- Barra lateral -->
        <div class="main-content">
            <header class="topbar">
                <h1>Panel de Gestión</h1>
            </header>
            <div class="content">
                <div class="widgets">
                    <div class="widget"><h3>Dispositivos en Inventario</h3><p>150 dispositivos</p></div>
                    <div class="widget"><h3>Dispositivos en Mantenimiento</h3><p>20 dispositivos</p></div>
                    <div class="widget"><h3>Inventario Crítico</h3><p>5 dispositivos con stock bajo</p></div>
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

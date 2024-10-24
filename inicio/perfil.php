<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // Si no hay sesión iniciada, redirigir al inicio de sesión
    header("Location: login.html");
    exit;
}

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // tu contraseña
$dbname = "empresa_inventario";

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
    <title>Perfil - Aulapp</title>
    <link rel="stylesheet" href="../css/perfil1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo">
                <img src="../img/rosaa-removebg-preview.png" alt="Logo de Inventario">
            </div>
            <ul>
                <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="home.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
                <li><a href="mantenimiento.php"><i class="fas fa-tools"></i> Mantenimiento</a></li>
                <li><a href="reportes.php"><i class="fas fa-chart-line"></i> Reportes</a></li>
                <li><a href="configuracion.php"><i class="fas fa-cog"></i> Informacion</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>

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
                    <h2>Bienvenido, <?php echo htmlspecialchars($userData['nombre'] . ' ' . $userData['apellido']); ?></h2>
                    <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($userData['telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($userData['direccion']); ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($userData['fecha_nacimiento']); ?></p>
                    <p><strong>Rol:</strong> <?php echo htmlspecialchars($userData['rol_nombre']); ?></p>
                    <img src="<?php echo htmlspecialchars($userData['foto_perfil']); ?>" alt="Foto de Perfil" style="width:100px; height:auto;">
                <?php else: ?>
                    <p>No se encontraron datos del usuario.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

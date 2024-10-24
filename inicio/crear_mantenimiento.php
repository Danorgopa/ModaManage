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

// Inicializar variables para almacenar las opciones
$equipos = [];
$tipos_mantenimiento = [];
$tecnicos = [];
$estados = [];

// Obtener equipos
$result = $conn->query("SELECT id, nombre FROM equipos");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $equipos[] = $row;
    }
}

// Obtener tipos de mantenimiento
$result = $conn->query("SELECT id, nombre FROM tipos_mantenimiento");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tipos_mantenimiento[] = $row;
    }
}

// Obtener técnicos
$result = $conn->query("SELECT id, nombre FROM tecnicos");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tecnicos[] = $row;
    }
}

// Obtener estados desde la tabla estados_equipos
$result = $conn->query("SELECT id, nombre FROM estados_equipos");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $estados[] = $row;
    }
}

// Comprobar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $equipo_id = $_POST['equipo_id']; 
    $tipo_mantenimiento_id = $_POST['tipo_mantenimiento_id']; 
    $descripcion = $_POST['descripcion']; 
    $tecnico_id = $_POST['tecnico_id']; 
    $estado_id = $_POST['estado_id']; 
    $ultimo_mantenimiento = $_POST['fecha_mantenimiento']; // Último mantenimiento
    $proximo_mantenimiento = $_POST['proximo_mantenimiento']; // Próximo mantenimiento

    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO mantenimientos (equipo_id, tipo_mantenimiento_id, descripcion, tecnico_id, estado_id, ultimo_mantenimiento, proximo_mantenimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Vincular parámetros
    $stmt->bind_param("iisssss", 
        $equipo_id, 
        $tipo_mantenimiento_id, 
        $descripcion, 
        $tecnico_id, 
        $estado_id, 
        $ultimo_mantenimiento, 
        $proximo_mantenimiento
    );

    // Ejecutar la declaración
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    // Cerrar la declaración
    $stmt->close();

    // Redireccionar a mantenimiento.php después de crear el mantenimiento
    header("Location: mantenimiento.php");
    exit(); 
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Mantenimiento</title>
    <link rel="stylesheet" href="../css/crear_mantenimiento2.css"> 
</head>
<body>
    <h1>Crear Mantenimiento</h1>
    <form method="POST" action="">
        <label for="equipo_id">Equipo:</label>
        <select name="equipo_id" required>
            <option value="">Seleccione un equipo</option>
            <?php foreach ($equipos as $equipo): ?>
                <option value="<?php echo $equipo['id']; ?>"><?php echo $equipo['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="tipo_mantenimiento_id">Tipo de Mantenimiento:</label>
        <select name="tipo_mantenimiento_id" required>
            <option value="">Seleccione un tipo de mantenimiento</option>
            <?php foreach ($tipos_mantenimiento as $tipo): ?>
                <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="fecha_mantenimiento">Fecha de Mantenimiento:</label>
        <input type="date" name="fecha_mantenimiento" required>
        
        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" required></textarea>
        
        <label for="tecnico_id">Técnico:</label>
        <select name="tecnico_id" required>
            <option value="">Seleccione un técnico</option>
            <?php foreach ($tecnicos as $tecnico): ?>
                <option value="<?php echo $tecnico['id']; ?>"><?php echo $tecnico['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="estado_id">Estado:</label>
        <select name="estado_id" required>
            <option value="">Seleccione un estado</option>
            <?php foreach ($estados as $estado): ?>
                <option value="<?php echo $estado['id']; ?>"><?php echo $estado['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="proximo_mantenimiento">Próximo Mantenimiento:</label>
        <input type="date" name="proximo_mantenimiento" required>

        <button type="submit">Crear Mantenimiento</button>
    </form>
</body>
</html>

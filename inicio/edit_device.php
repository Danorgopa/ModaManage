<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "empresa_inventario"); // Conexión con la base de datos

// Verificar si se está actualizando un dispositivo
if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $estado_id = $_POST['estado']; // Recoger el nuevo estado del formulario

    // Actualizar los datos del dispositivo
    $conn->query("UPDATE equipos SET nombre='$nombre', descripcion='$descripcion', cantidad='$cantidad', precio='$precio' WHERE id = $id");

    // Actualizar el estado del mantenimiento (asumiendo que solo hay un mantenimiento activo por equipo)
    $conn->query("UPDATE mantenimientos SET estado_id='$estado_id' WHERE equipo_id = $id");

    // Redirigir a inventario.php después de guardar los cambios
    header("Location: inventario.php");
    exit;
}

// Obtener datos del dispositivo a editar
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM equipos WHERE id = $id");
$row = $result->fetch_assoc();

// Obtener el estado actual desde la tabla `mantenimientos` (asumiendo que solo hay un mantenimiento activo)
$mantenimiento_result = $conn->query("SELECT estado_id FROM mantenimientos WHERE equipo_id = $id");
$mantenimiento_row = $mantenimiento_result->fetch_assoc();
$estado_actual_id = $mantenimiento_row ? $mantenimiento_row['estado_id'] : null;

// Obtener los estados disponibles desde la tabla `estados_equipos`
$estados_result = $conn->query("SELECT * FROM estados_equipos");
$estados = [];
while ($estado = $estados_result->fetch_assoc()) {
    $estados[] = $estado;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dispositivo</title>
    <link rel="stylesheet" href="../css/edit_device3.css">
</head>
<body>
    <div class="edit-device-container">
        <h2>Editar Dispositivo</h2>
        <form method="POST" action="edit_device.php?id=<?php echo $row['id']; ?>"> <!-- Mantener la acción en la misma página -->
            <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea><br><br>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" required><br><br>

            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>" required><br><br>

            <!-- Campo para seleccionar el estado del dispositivo -->
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="select" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?php echo $estado['id']; ?>" <?php if ($estado['id'] == $estado_actual_id) echo 'selected'; ?>>
                        <?php echo $estado['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="submit" value="Guardar Cambios">
            <button type="button" onclick="location.href='inventario.php'">Regresar</button> <!-- Botón para regresar -->
        </form>
    </div>
</body>
</html>

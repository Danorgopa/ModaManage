<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "empresa_inventario"); // Conexión con la base de datos

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se está eliminando un dispositivo
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Escapar el ID para evitar inyecciones SQL
    $delete_id = $conn->real_escape_string($delete_id);

    // Iniciar una transacción para asegurar la integridad de los datos
    $conn->begin_transaction();

    try {
        // Eliminar primero todas las relaciones del equipo en las tablas relacionadas
        // 1. Eliminar de la tabla fechas_productos
        $conn->query("DELETE FROM fechas_productos WHERE producto_equipo_id = $delete_id");

        // 2. Eliminar de la tabla mantenimientos
        $conn->query("DELETE FROM mantenimientos WHERE equipo_id = $delete_id");

        // 3. Eliminar finalmente el equipo de la tabla equipos
        $conn->query("DELETE FROM equipos WHERE id = $delete_id");

        // Confirmar los cambios
        $conn->commit();

        // Redireccionar después de eliminar
        header("Location: inventario.php");
        exit;

    } catch (Exception $e) {
        // Si ocurre algún error, revertir la transacción
        $conn->rollback();
        die("Error al eliminar el dispositivo: " . $e->getMessage());
    }
}

// Verificar si se está actualizando un dispositivo
if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    // Escapar los valores para evitar inyecciones SQL
    $id = $conn->real_escape_string($id);
    $nombre = $conn->real_escape_string($nombre);
    $descripcion = $conn->real_escape_string($descripcion);
    $cantidad = $conn->real_escape_string($cantidad);
    $precio = $conn->real_escape_string($precio);

    // Actualizar la información del equipo
    $conn->query("UPDATE equipos SET nombre='$nombre', descripcion='$descripcion', cantidad='$cantidad', precio='$precio' WHERE id = $id");

    // Redireccionar después de la actualización
    header("Location: inventario.php");
    exit;
}

// Consulta para obtener los dispositivos y su estado actual
$result = $conn->query("
    SELECT e.*, 
            COALESCE(m.estado_id, NULL) AS estado_id, 
            COALESCE(es.nombre, 'Sin estado') AS estado_nombre
    FROM equipos e
    LEFT JOIN (
        SELECT equipo_id, estado_id 
        FROM mantenimientos 
        WHERE id IN (SELECT MAX(id) FROM mantenimientos GROUP BY equipo_id)
    ) m ON e.id = m.equipo_id
    LEFT JOIN estados_equipos es ON m.estado_id = es.id
");

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$totalPrecio = 0; // Variable para almacenar el precio total
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aulapp - Inventario</title>
    <link rel="stylesheet" href="../css/inventario1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <header class="topbar">
                <h1>Gestión de Inventario</h1>
            </header>
            <div class="content">
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Dispositivo</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Precio (UNI)</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $precioTotal = $row['precio'] * $row['cantidad']; // Calcular precio total por dispositivo
                                $totalPrecio += $precioTotal; // Calcular precio total acumulado
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($row['precio']); ?></td>
                                <td><?php echo number_format($precioTotal, 2); ?> USD</td>
                                <td><?php echo htmlspecialchars($row['estado_nombre']); ?></td> <!-- Mostrar el estado del dispositivo -->
                                <td>
                                    <a href="edit_device.php?id=<?php echo $row['id']; ?>" class="edit-btn">Editar</a> | 
                                    <a href="inventario.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar este dispositivo?');">Eliminar</a> 
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No hay dispositivos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <h2>Precio Total General: <?php echo number_format($totalPrecio, 2); ?> USD</h2>
            </div>
        </div>
    </div>

    <script>
        // Aquí puedes agregar cualquier script adicional si es necesario
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>

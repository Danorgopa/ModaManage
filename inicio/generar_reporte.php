<?php
// Iniciar sesión
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

// Conexión a la base de datos
$conn = new mysqli("127.0.0.1", "root", "", "empresa_inventario");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo de la búsqueda
$busqueda = '';
if (isset($_POST['buscar'])) {
    $busqueda = $conn->real_escape_string($_POST['busqueda']);
}

// Consulta SQL para obtener datos de los dispositivos, incluyendo el estado y la fecha de compra
$sql = "SELECT e.nombre, e.descripcion, e.cantidad, e.precio, 
               (e.cantidad * e.precio) AS precio_total,  
                p.nombre AS proveedor, c.nombre AS categoria, m.nombre AS marca, 
                es.nombre AS estado, fp.fecha_compra  
        FROM equipos e
        JOIN proveedores p ON e.proveedor_id = p.id
        JOIN categorias c ON e.categoria_id = c.id
        JOIN marcas m ON e.marca_id = m.id
        LEFT JOIN mantenimientos mt ON e.id = mt.equipo_id  
        LEFT JOIN estados_equipos es ON mt.estado_id = es.id  
        LEFT JOIN fechas_productos fp ON e.id = fp.producto_equipo_id";

if (!empty($busqueda)) {
    $sql .= " WHERE e.nombre LIKE '%$busqueda%'";
}

// Ejecutar la consulta y verificar si hubo un error
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);  // Mostrar el error de la consulta
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte</title>
    <link rel="stylesheet" href="../css/btn_repotes3.css">
    <link rel="stylesheet" href="../css/buscador.css"> <!-- Incluye el archivo CSS del buscador -->
</head>
<body>
    <!-- Capa semi-transparente -->
    <div class="overlay"></div>

    <!-- Contenido principal -->
    <div class="container">
        <h2>Reporte de Dispositivos</h2>

        <!-- Formulario de búsqueda -->
        <form method="POST">
            <input type="text" name="busqueda" placeholder="Buscar dispositivo por nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" name="buscar">Buscar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Precio Total</th> <!-- Nueva columna para Precio Total -->
                    <th>Proveedor</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Estado</th> <!-- Nueva columna para Estado -->
                    <th>Fecha de Compra</th> <!-- Nueva columna para Fecha de Compra -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['nombre']) . "</td>
                                <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                <td>" . htmlspecialchars($row['cantidad']) . "</td>
                                <td>" . number_format($row['precio'], 2) . "</td>
                                <td>" . number_format($row['precio_total'], 2) . "</td> <!-- Mostrar precio total -->
                                <td>" . htmlspecialchars($row['proveedor']) . "</td>
                                <td>" . htmlspecialchars($row['categoria']) . "</td>
                                <td>" . htmlspecialchars($row['marca']) . "</td>
                                <td>" . htmlspecialchars($row['estado']) . "</td> <!-- Mostrar estado -->
                                <td>" . htmlspecialchars($row['fecha_compra']) . "</td> <!-- Mostrar fecha de compra -->
                                </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No hay dispositivos registrados</td></tr>"; // Cambiar a colspan='10'
                }
                ?>
            </tbody>
        </table>

        <!-- Botón de regreso -->
        <button type="button" class="btn" onclick="window.location.href='home.php';">Regresar al Home</button>
    </div>
</body>
</html>

<?php
$conn->close();
?>

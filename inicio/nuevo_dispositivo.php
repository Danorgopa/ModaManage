<?php
session_start(); // Iniciar sesión para almacenar mensajes

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'empresa_inventario');

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inserción de categorías (solo si no existen)
$categorias = [
    'Computadoras',
    'Impresoras',
    'Equipos de red',
    'Televisores',
    'Sistemas de audio',
    'Proyectores',
    'Otros dispositivos electrónicos'
];

foreach ($categorias as $categoria) {
    $sql_categoria = "INSERT IGNORE INTO categorias (nombre) VALUES ('$categoria')";
    $conn->query($sql_categoria);
}

// Inserción de marcas (solo si no existen)
$marcas = [
    'Samsung',
    'LG',
    'HP',
    'Lenovo',
    'Dell',
    'Epson',
    'Canon',
    'Xerox',
    'Asus',
    'Motorola'
];

foreach ($marcas as $marca) {
    $sql_marca = "INSERT IGNORE INTO marcas (nombre) VALUES ('$marca')";
    $conn->query($sql_marca);
}

// Inserción de proveedores (solo si no existen)
$proveedores = [
    ['Tech Solutions S.A.S.', 'Juan Pérez', '3123456789', 'Carrera 10 #20-30', 'info@techsolutions.com'],
    ['Electro World Ltda.', 'María Gómez', '3219876543', 'Avenida 5 #10-20', 'contacto@electroworld.com'],
    ['Innovación Digital S.A.', 'Carlos Ruiz', '3101234567', 'Calle 25 #12-34', 'info@innovaciondigital.com'],
    ['Sistemas Avanzados S.A.S.', 'Ana Torres', '3174567890', 'Carrera 50 #30-40', 'info@sistemasavanzados.com'],
    ['Red de Soluciones Electrónicas', 'Luis Martínez', '3136547890', 'Avenida 15 #22-25', 'info@redsoluciones.com'],
    ['Dispositivos y Tecnología S.A.S.', 'Julián Morales', '3145678901', 'Calle 10 #15-20', 'info@dispositivosyt.com'],
    ['Soluciones Electrónicas de Colombia', 'Sofía León', '3112345678', 'Calle 30 #14-18', 'contacto@solucioneselectronicas.com'],
    ['Grupo Electrónico S.A.S.', 'Diego López', '3159876543', 'Carrera 12 #5-10', 'info@grupoelectronico.com'],
    ['Almacenamiento y Redes S.A.', 'Claudia Romero', '3128765432', 'Avenida 40 #30-45', 'info@almacenamiento.com'],
    ['Electrocomp S.A.S.', 'Esteban Rojas', '3107890123', 'Calle 22 #10-15', 'info@electrocomp.com']
];

foreach ($proveedores as $proveedor) {
    $sql_proveedor = "INSERT IGNORE INTO proveedores (nombre, contacto, telefono, direccion, email) VALUES ('$proveedor[0]', '$proveedor[1]', '$proveedor[2]', '$proveedor[3]', '$proveedor[4]')";
    $conn->query($sql_proveedor);
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $proveedor = $_POST['proveedor'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];
    $precio = $_POST['precio'];
    $caracteristicas = $_POST['caracteristicas'];
    $fecha_compra = $_POST['fecha_compra'];
    $fecha_garantia = $_POST['fecha_garantia'];
    $fecha_vida_util = $_POST['fecha_vida_util'];

    // Calcular el precio total
    $precio_total = $precio * $cantidad;

    // Asignar el estado "libre" (asumiendo que el id es 2)
    $estado_id = 2; // ID del estado "libre" en la tabla `estados_equipos`

    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre) || empty($descripcion) || empty($cantidad) || empty($proveedor) || empty($categoria) || empty($marca) || empty($precio)) {
        $_SESSION['message'] = "Por favor, completa todos los campos obligatorios.";
        header('Location: mensaje.php');
        exit();
    }

    // Generar un código único
    $codigo_unico = uniqid('eq_'); // Prefijo 'eq_' seguido de un ID único

    // Preparar la consulta de inserción en la tabla equipos
    $sql = $conn->prepare("INSERT INTO equipos (nombre, descripcion, cantidad, proveedor_id, categoria_id, marca_id, precio, precio_total, caracteristicas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Verificar si la preparación fue exitosa
    if (!$sql) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Asignar parámetros
    $sql->bind_param('ssiiiddss', $nombre, $descripcion, $cantidad, $proveedor, $categoria, $marca, $precio, $precio_total, $caracteristicas);

    // Ejecutar la consulta
    if ($sql->execute()) {
        // Obtener el ID del dispositivo insertado
        $producto_equipo_id = $conn->insert_id;

        // Ahora insertar en la tabla fechas_productos
        $sql_fecha = $conn->prepare("INSERT INTO fechas_productos (producto_equipo_id, fecha_compra, fecha_garantia, fecha_vida_util, codigo_unico_del_equipo) VALUES (?, ?, ?, ?, ?)");

        // Verificar si la preparación fue exitosa
        if (!$sql_fecha) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        // Asignar parámetros para la tabla fechas_productos
        $sql_fecha->bind_param('issss', $producto_equipo_id, $fecha_compra, $fecha_garantia, $fecha_vida_util, $codigo_unico);

        // Ejecutar la consulta
        if ($sql_fecha->execute()) {
            // Ahora insertar en la tabla mantenimientos
            $sql_mantenimiento = $conn->prepare("INSERT INTO mantenimientos (equipo_id, estado_id) VALUES (?, ?)");

            // Verificar si la preparación fue exitosa
            if (!$sql_mantenimiento) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            // Asignar parámetros para la tabla mantenimientos
            $sql_mantenimiento->bind_param('ii', $producto_equipo_id, $estado_id);

            // Ejecutar la consulta de mantenimiento
            if ($sql_mantenimiento->execute()) {
                $_SESSION['message'] = "Dispositivo añadido exitosamente con estado 'libre' y código único: $codigo_unico.";
            } else {
                $_SESSION['message'] = "Error al añadir mantenimiento: " . $conn->error;
            }

            // Cerrar la consulta de mantenimiento
            $sql_mantenimiento->close();
        } else {
            $_SESSION['message'] = "Error al añadir fechas del dispositivo: " . $conn->error;
        }

        // Cerrar la consulta de fechas
        $sql_fecha->close();
    } else {
        $_SESSION['message'] = "Error al añadir dispositivo: " . $conn->error;
    }

    // Cerrar la consulta de equipos
    $sql->close();
    // Redirigir a la página de mensaje
    header('Location: mensaje.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Dispositivo</title>
    <link rel="stylesheet" href="../css/nuevos_dps2.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h2>Añadir Nuevo Dispositivo</h2>
        <form action="nuevo_dispositivo.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required><br>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required></textarea><br>

            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidad" required><br>

            <label for="proveedor">Proveedor:</label>
            <select name="proveedor" required>
                <?php
                // Obtener los proveedores de la base de datos
                $sql_proveedores = "SELECT id, nombre FROM proveedores";
                $result_proveedores = $conn->query($sql_proveedores);
                while ($row = $result_proveedores->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select><br>

            <label for="categoria">Categoría:</label>
            <select name="categoria" required>
                <?php
                // Obtener las categorías de la base de datos
                $sql_categorias = "SELECT id, nombre FROM categorias";
                $result_categorias = $conn->query($sql_categorias);
                while ($row = $result_categorias->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select><br>

            <label for="marca">Marca:</label>
            <select name="marca" required>
                <?php
                // Obtener las marcas de la base de datos
                $sql_marcas = "SELECT id, nombre FROM marcas";
                $result_marcas = $conn->query($sql_marcas);
                while ($row = $result_marcas->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select><br>

            <label for="precio">Precio:</label>
            <input type="number" name="precio" step="0.01" required><br>

            <label for="caracteristicas">Características:</label>
            <textarea name="caracteristicas"></textarea><br>

            <label for="fecha_compra">Fecha de Compra:</label>
            <input type="date" name="fecha_compra" required><br>

            <label for="fecha_garantia">Fecha de Garantía:</label>
            <input type="date" name="fecha_garantia" required><br>

            <label for="fecha_vida_util">Fecha de Vida Útil:</label>
            <input type="date" name="fecha_vida_util" required><br>

            <input type="submit" value="Añadir Dispositivo">
            <button type="button" class="btn" onclick="window.location.href='home.php';">Regresar al Home</button>
        </form>
    </div>
</body>
</html>

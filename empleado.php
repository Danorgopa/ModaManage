<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.html");
  exit;
}

// Conexión a la base de datos
$conn = new mysqli("127.0.0.1", "root", "", "empresa_inventario");

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado el ID del empleado para "eliminar"
if (isset($_GET['cedula'])) {
  $id = intval($_GET['cedula']); // Convertir a entero para evitar inyecciones SQL

  // Consulta SQL para actualizar el estado del empleado a 0 (inactivo)
  $sql = "UPDATE empleados SET estado = 0 WHERE cedula = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt) {
    $stmt->bind_param("i", $id); // "i" indica que el parámetro es un entero

    if ($stmt->execute()) {
      // Mensaje de confirmación (opcional)
      echo "<script>
              alert('Empleado desactivado correctamente.');
              window.location.href = 'empleado.php';
            </script>";
      exit;
    } else {
      // Mostrar mensaje de error
      echo "<script>
              alert('Error al desactivar el empleado: " . $stmt->error . "');
            </script>";
    }

    $stmt->close();
  } else {
    echo "<script>
            alert('Error al preparar la consulta: " . $conn->error . "');
          </script>";
  }
}

// Consulta SQL para obtener datos de empleados activos
$sql = "SELECT cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
        direccion, fecha_nacimiento, licencia_conduccion, fecha_ingreso, experiencia, 
        edad, estado_civil, rh, eps, arl, estrato, profesion, rol_id 
        FROM empleados WHERE rol_id = 1";

$result = $conn->query($sql);

// Manejo de errores
if (!$result) {
  die("Error en la consulta: " . $conn->error);
}



?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ERP - Empleados</title>
  <link rel="stylesheet" href="css/inventario1.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Iconos -->
</head>

<body>
  <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <header class="topbar">
        <h1>Gestión de Empleados</h1>
        <div class="actions">
          <a href="crear_empleado.php" class="btn btn-primary">Ingresar Empleado</a>
          <a href="logout.php" class="btn">Salir</a>
        </div>
      </header>
      <div class="content">
        <table class="doc-table">
          <thead>
            <tr>
              <th>Cédula</th>
              <th>Primer Nombre</th>
              <th>Segundo Nombre</th>
              <th>Primer Apellido</th>
              <th>Segundo Apellido</th>
              <th>Dirección</th>
              <th>Fecha de Nacimiento</th>
              <th>Licencia de Conducción</th>
              <th>Fecha de Ingreso</th>
              <th>Experiencia</th>
              <th>Edad</th>
              <th>Estado Civil</th>
              <th>RH</th>
              <th>EPS</th>
              <th>ARL</th>
              <th>Estrato</th>
              <th>Profesión</th>
              <th>Rol ID</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                      <td>" . htmlspecialchars($row['cedula']) . "</td>
                      <td>" . htmlspecialchars($row['primer_nombre']) . "</td>
                      <td>" . htmlspecialchars($row['segundo_nombre']) . "</td>
                      <td>" . htmlspecialchars($row['primer_apellido']) . "</td>
                      <td>" . htmlspecialchars($row['segundo_apellido']) . "</td>
                      <td>" . htmlspecialchars($row['direccion']) . "</td>
                      <td>" . htmlspecialchars($row['fecha_nacimiento']) . "</td>
                      <td>" . htmlspecialchars($row['licencia_conduccion']) . "</td>
                      <td>" . htmlspecialchars($row['fecha_ingreso']) . "</td>
                      <td>" . htmlspecialchars($row['experiencia']) . "</td>
                      <td>" . htmlspecialchars($row['edad']) . "</td>
                      <td>" . htmlspecialchars($row['estado_civil']) . "</td>
                      <td>" . htmlspecialchars($row['rh']) . "</td>
                      <td>" . htmlspecialchars($row['eps']) . "</td>
                      <td>" . htmlspecialchars($row['arl']) . "</td>
                      <td>" . htmlspecialchars($row['estrato']) . "</td>
                      <td>" . htmlspecialchars($row['profesion']) . "</td>
                      <td>" . htmlspecialchars($row['rol_id']) . "</td>
                      <td>
                        <a href='empleado_editar.php?cedula=" . htmlspecialchars($row['cedula']) . "' title='Editar' class='btn btn-primary'>
                          <i class='fa fa-edit'></i>
                        </a>
                        <a href='empleado.php?cedula=" . htmlspecialchars($row['cedula']) . "' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este empleado?\");'>
                          <i class='fa fa-trash'></i> 
                        </a>
                      </td>
                    </tr>";
              }
            } else {
              echo "<tr><td colspan='19'>No hay empleados registrados</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>

<?php
$conn->close();
?>
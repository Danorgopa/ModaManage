<?php
session_start();

// Verificar si el usuario ha iniciado sesión y si tiene el rol adecuado
if (!isset($_SESSION['username']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
  // Redirigir a home.php si no tiene rol de administrador (rol_id != 1)
  header("Location: home.php");
  exit;
}

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

// Obtener los datos del empleado si se proporciona una cédula
$cedula = "";
$empleado = null;
if (isset($_GET['cedula'])) {
  $cedula = intval($_GET['cedula']); // Convertir a entero para seguridad

  // Consulta para obtener los datos del empleado (sin preparación de parámetros ya que la variable está directa)
  $sql = "SELECT * FROM empleados WHERE cedula = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
  }

  $stmt->bind_param("i", $cedula);
  $stmt->execute();
  $result = $stmt->get_result();
  $empleado = $result->fetch_assoc();
  $stmt->close();
}

// Comprobar si se han enviado los datos del formulario para actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger datos del formulario
  $cedula = $_POST['cedula'];
  $primer_nombre = $_POST['primer_nombre'];
  $segundo_nombre = $_POST['segundo_nombre'];
  $primer_apellido = $_POST['primer_apellido'];
  $segundo_apellido = $_POST['segundo_apellido'];
  $direccion = $_POST['direccion'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];
  $fecha_ingreso = $_POST['fecha_ingreso'];
  $experiencia = $_POST['experiencia'];
  $edad = $_POST['edad'];
  $estado_civil = $_POST['estado_civil'];
  $rh = $_POST['rh'];
  $eps = $_POST['eps'];
  $arl = $_POST['arl'];
  $estrato = $_POST['estrato'];
  $profesion = $_POST['profesion'];
  $rol_id = $_POST['rol_id'];

  // Manejar el valor de la licencia de conducción
  if (isset($_POST['licencia_si_no']) && $_POST['licencia_si_no'] === 'si') {
    if (isset($_POST['tipo_vehiculo']) && $_POST['tipo_vehiculo'] === 'moto') {
      $licencia_conduccion = $_POST['licencia_grupo1'];
    } elseif (isset($_POST['tipo_vehiculo']) && $_POST['tipo_vehiculo'] === 'carro') {
      $licencia_conduccion = $_POST['licencia_grupo2'];
    } else {
      $licencia_conduccion = "NO";
    }
  } else {
    $licencia_conduccion = "NO";
  }

  // Actualizar los datos del empleado
  $stmt = $conn->prepare("UPDATE empleados SET primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?, direccion = ?, fecha_nacimiento = ?, licencia_conduccion = ?, fecha_ingreso = ?, experiencia = ?, edad = ?, estado_civil = ?, rh = ?, eps = ?, arl = ?, estrato = ?, profesion = ?, rol_id = ? WHERE cedula = ?");

  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }

  // Vincular parámetros
  $stmt->bind_param(
    "ssssssssiiisssssii",
    $primer_nombre,
    $segundo_nombre,
    $primer_apellido,
    $segundo_apellido,
    $direccion,
    $fecha_nacimiento,
    $licencia_conduccion,
    $fecha_ingreso,
    $experiencia,
    $edad,
    $estado_civil,
    $rh,
    $eps,
    $arl,
    $estrato,
    $profesion,
    $rol_id,
    $cedula
  );

  // Ejecutar la declaración
  if ($stmt->execute()) {
    // Mostrar mensaje de éxito y redirigir a mantenimiento.php
    echo "<script>
            alert('Registro actualizado correctamente.');
            window.location.href = 'empleado.php';
          </script>";
  } else {
    die("Error executing statement: " . $stmt->error);
  }

  // Cerrar la declaración
  $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Empleado</title>
  <link rel="stylesheet" href="css/nuevos_dps2.css">
  <link rel="stylesheet" href="css/general_sidebar.css">
  <style>
    .hidden {
      display: none;
    }
  </style>
  <script>
    function toggleLicenciaOptions() {
      const licenciaSiNo = document.querySelector('input[name="licencia_si_no"]:checked');
      const licenciaDetails = document.getElementById('licencia-details');
      const motoCarro = document.getElementById('moto-carro');
      const grupo1 = document.getElementById('grupo1');
      const grupo2 = document.getElementById('grupo2');

      if (licenciaSiNo && licenciaSiNo.value === 'si') {
        licenciaDetails.classList.remove('hidden');
      } else {
        licenciaDetails.classList.add('hidden');
        motoCarro.classList.add('hidden');
        grupo1.classList.add('hidden');
        grupo2.classList.add('hidden');
      }
    }

    function toggleMotoCarroOptions() {
      const tipoVehiculo = document.querySelector('input[name="tipo_vehiculo"]:checked');
      const grupo1 = document.getElementById('grupo1');
      const grupo2 = document.getElementById('grupo2');

      if (tipoVehiculo) {
        if (tipoVehiculo.value === 'moto') {
          grupo1.classList.remove('hidden');
          grupo2.classList.add('hidden');
        } else if (tipoVehiculo.value === 'carro') {
          grupo2.classList.remove('hidden');
          grupo1.classList.add('hidden');
        }
      }
    }
  </script>
</head>

<body>
  <div class="overlay"></div>
  <div class="container">
    <h2>Editar Empleado</h2>
    <form action="" method="post">
      <label for="cedula">Cédula:</label>
      <input type="text" name="cedula" value="<?php echo isset($empleado['cedula']) ? htmlspecialchars($empleado['cedula']) : ''; ?>" readonly><br>

      <label for="primer_nombre">Primer Nombre:</label>
      <input type="text" name="primer_nombre" value="<?php echo isset($empleado['primer_nombre']) ? htmlspecialchars($empleado['primer_nombre']) : ''; ?>" required><br>

      <label for="segundo_nombre">Segundo Nombre:</label>
      <input type="text" name="segundo_nombre" value="<?php echo isset($empleado['segundo_nombre']) ? htmlspecialchars($empleado['segundo_nombre']) : ''; ?>"><br>

      <label for="primer_apellido">Primer Apellido:</label>
      <input type="text" name="primer_apellido" value="<?php echo isset($empleado['primer_apellido']) ? htmlspecialchars($empleado['primer_apellido']) : ''; ?>" required><br>

      <label for="segundo_apellido">Segundo Apellido:</label>
      <input type="text" name="segundo_apellido" value="<?php echo isset($empleado['segundo_apellido']) ? htmlspecialchars($empleado['segundo_apellido']) : ''; ?>"><br>

      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion" value="<?php echo isset($empleado['direccion']) ? htmlspecialchars($empleado['direccion']) : ''; ?>"><br>

      <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
      <input type="date" name="fecha_nacimiento" value="<?php echo isset($empleado['fecha_nacimiento']) ? htmlspecialchars($empleado['fecha_nacimiento']) : ''; ?>" required><br>

      <label for="fecha_ingreso">Fecha de Ingreso:</label>
      <input type="date" name="fecha_ingreso" value="<?php echo isset($empleado['fecha_ingreso']) ? htmlspecialchars($empleado['fecha_ingreso']) : ''; ?>" required><br>

      <label for="experiencia">Años de Experiencia:</label>
      <input type="number" name="experiencia" value="<?php echo isset($empleado['experiencia']) ? htmlspecialchars($empleado['experiencia']) : ''; ?>" min="0"><br>

      <label for="edad">Edad:</label>
      <input type="number" name="edad" value="<?php echo isset($empleado['edad']) ? htmlspecialchars($empleado['edad']) : ''; ?>" min="0"><br>

      <label for="estado_civil">Estado Civil:</label>
      <select name="estado_civil">
        <option value="soltero" <?php echo (isset($empleado['estado_civil']) && $empleado['estado_civil'] === 'soltero') ? 'selected' : ''; ?>>Soltero</option>
        <option value="casado" <?php echo (isset($empleado['estado_civil']) && $empleado['estado_civil'] === 'casado') ? 'selected' : ''; ?>>Casado</option>
        <option value="divorciado" <?php echo (isset($empleado['estado_civil']) && $empleado['estado_civil'] === 'divorciado') ? 'selected' : ''; ?>>Divorciado</option>
        <option value="viudo" <?php echo (isset($empleado['estado_civil']) && $empleado['estado_civil'] === 'viudo') ? 'selected' : ''; ?>>Viudo</option>
      </select><br>

      <label for="rh">RH:</label>
      <input type="text" name="rh" value="<?php echo isset($empleado['rh']) ? htmlspecialchars($empleado['rh']) : ''; ?>"><br>

      <label for="eps">EPS:</label>
      <input type="text" name="eps" value="<?php echo isset($empleado['eps']) ? htmlspecialchars($empleado['eps']) : ''; ?>"><br>

      <label for="arl">ARL:</label>
      <input type="text" name="arl" value="<?php echo isset($empleado['arl']) ? htmlspecialchars($empleado['arl']) : ''; ?>"><br>

      <label for="estrato">Estrato:</label>
      <input type="number" name="estrato" value="<?php echo isset($empleado['estrato']) ? htmlspecialchars($empleado['estrato']) : ''; ?>" min="1" max="6"><br>

      <label for="profesion">Profesión:</label>
      <input type="text" name="profesion" value="<?php echo isset($empleado['profesion']) ? htmlspecialchars($empleado['profesion']) : ''; ?>"><br>

      <label for="rol_id">Cargo:</label>
      <select name="rol_id" required>
        <option value="1" <?php echo (isset($empleado['rol_id']) && $empleado['rol_id'] == 1) ? 'selected' : ''; ?>>Administrador</option>
        <option value="2" <?php echo (isset($empleado['rol_id']) && $empleado['rol_id'] == 2) ? 'selected' : ''; ?>>Inventarista</option>
      </select><br>

      <input type="submit" value="Actualizar Empleado">
      <button type="button" class="btn" onclick="window.location.href='home.php';">Regresar al Home</button>
    </form>
  </div>
</body>

</html>
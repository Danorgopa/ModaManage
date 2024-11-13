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

// Comprobar si se han enviado los datos del formulario
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

  // Preparar la consulta SQL
  $stmt = $conn->prepare("INSERT INTO empleados (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, direccion, fecha_nacimiento, licencia_conduccion, fecha_ingreso, experiencia, edad, estado_civil, rh, eps, arl, estrato, profesion, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }

  // Vincular parámetros
  $stmt->bind_param(
    "issssssssiiisssssi",
    $cedula,
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
    $rol_id
  );

  // Ejecutar la declaración
  if ($stmt->execute()) {
    // Mostrar mensaje de éxito y redirigir a mantenimiento.php
    echo "<script>
            alert('Registro creado correctamente.');
            window.location.href = 'mantenimiento.php';
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
  <title>Añadir Nuevo Empleado</title>
  
  <link rel="stylesheet" href="css/nuevos_dps2.css">
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
    <h2>Añadir Nuevo Empleado</h2>
    <form action="" method="post">
      <label for="cedula">Cédula:</label>
      <input type="text" name="cedula" required><br>

      <label for="primer_nombre">Primer Nombre:</label>
      <input type="text" name="primer_nombre" required><br>

      <label for="segundo_nombre">Segundo Nombre:</label>
      <input type="text" name="segundo_nombre"><br>

      <label for="primer_apellido">Primer Apellido:</label>
      <input type="text" name="primer_apellido" required><br>

      <label for="segundo_apellido">Segundo Apellido:</label>
      <input type="text" name="segundo_apellido"><br>

      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion"><br>

      <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
      <input type="date" name="fecha_nacimiento" required><br>

      <!-- Licencia de Conducción -->
      <label for="licencia_conduccion">¿Posee Licencia de Conducción?</label><br>
      <input type="radio" name="licencia_si_no" value="si" onchange="toggleLicenciaOptions()"> Sí
      <input type="radio" name="licencia_si_no" value="no" onchange="toggleLicenciaOptions()"> No<br>

      <div id="licencia-details" class="hidden">
        <label>Tipo de Vehículo:</label><br>
        <input type="radio" name="tipo_vehiculo" value="moto" onchange="toggleMotoCarroOptions()"> Moto
        <input type="radio" name="tipo_vehiculo" value="carro" onchange="toggleMotoCarroOptions()"> Carro<br>

        <div id="grupo1" class="hidden">
          <label for="licencia_grupo1">Licencias de Grupo 1:</label><br>
          <select name="licencia_grupo1">
            <option value="A1">A1</option>
            <option value="A2">A2</option>
            <option value="B1">B1</option>
          </select><br>
        </div>

        <div id="grupo2" class="hidden">
          <label for="licencia_grupo2">Licencias de Grupo 2:</label><br>
          <select name="licencia_grupo2">
            <option value="B2">B2</option>
            <option value="B3">B3</option>
            <option value="C1">C1</option>
            <option value="C2">C2</option>
            <option value="C3">C3</option>
          </select><br>
        </div>
      </div>

      <!-- Continuar con el formulario -->
      <label for="fecha_ingreso">Fecha de Ingreso:</label>
      <input type="date" name="fecha_ingreso" required><br>

      <label for="experiencia">Años de Experiencia:</label>
      <input type="number" name="experiencia" min="0"><br>

      <label for="edad">Edad:</label>
      <input type="number" name="edad" min="0"><br>

      <label for="estado_civil">Estado Civil:</label>
      <select name="estado_civil">
        <option value="soltero">Soltero</option>
        <option value="casado">Casado</option>
        <option value="divorciado">Divorciado</option>
        <option value="viudo">Viudo</option>
      </select><br>

      <label for="rh">RH:</label>
      <input type="text" name="rh"><br>

      <label for="eps">EPS:</label>
      <input type="text" name="eps"><br>

      <label for="arl">ARL:</label>
      <input type="text" name="arl"><br>

      <label for="estrato">Estrato:</label>
      <input type="number" name="estrato" min="1" max="6"><br>

      <label for="profesion">Profesión:</label>
      <input type="text" name="profesion"><br>

      <label for="rol_id">Cargo:</label>
      <select name="rol_id" required>
        <option value="1" selected>Administrador</option>
        <option value="2">inventarista</option>

      </select><br>


      <input type="submit" value="Añadir Empleado">
      <button type="button" class="btn" onclick="window.location.href='empleado.php';">Regresar</button>
    </form>
  </div>
</body>

</html>
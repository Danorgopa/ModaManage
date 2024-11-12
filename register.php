<?php
// Iniciar la sesión
session_start();

// Conectar a la base de datos
$servername = "127.0.0.1";
$username_db = "if0_37701389";
$password_db = "mvOCfmjvomz";
$dbname = "if0_37701389_empresa_inventario";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$email = $_POST['email'];
$nombre = $_POST['nombre'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$rol_input = $_POST['rol'];

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
  echo "Las contraseñas no coinciden.";
  exit;
}

// Verificar si el nombre de usuario ya existe
$check_username_query = "SELECT * FROM login WHERE username = ?";
$stmt_check_username = $conn->prepare($check_username_query);
$stmt_check_username->bind_param("s", $username);
$stmt_check_username->execute();
$result = $stmt_check_username->get_result();

if ($result->num_rows > 0) {
  header("Location: register.html");
  exit;
}

// Determinar el rol según el código ingresado
$rol = 5; // Por defecto será 'usuarios'
if (!empty($rol_input)) {
  switch ($rol_input) {
    case 109:
      $rol = 1;
      break;
    case 619:
      $rol = 2;
      break;
    case 226:
      $rol = 3;
      break;
    case 610:
      $rol = 4;
      break;
  }
}

// Encriptar la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
  // Iniciar transacción
  $conn->begin_transaction();

  // Insertar datos en la tabla `usuarios`
  $sql_usuarios = "INSERT INTO usuarios (email, nombre, rol_id) VALUES (?, ?, ?)";
  $stmt_usuarios = $conn->prepare($sql_usuarios);
  if (!$stmt_usuarios) {
    throw new Exception("Error en la preparación de la consulta para usuarios: " . $conn->error);
  }
  $stmt_usuarios->bind_param("ssi", $email, $nombre, $rol);

  if ($stmt_usuarios->execute()) {
    $usuario_id = $stmt_usuarios->insert_id;

    // Insertar datos en la tabla `login`
    $sql_login = "INSERT INTO login (usuario_id, username, password) VALUES (?, ?, ?)";
    $stmt_login = $conn->prepare($sql_login);
    if (!$stmt_login) {
      throw new Exception("Error en la preparación de la consulta para login: " . $conn->error);
    }
    $stmt_login->bind_param("iss", $usuario_id, $username, $hashed_password);

    if ($stmt_login->execute()) {
      $conn->commit();
      header("Location: index.html");
      exit();
    } else {
      throw new Exception("Error al insertar en la tabla login: " . $conn->error);
    }
  } else {
    throw new Exception("Error al insertar en la tabla usuarios: " . $conn->error);
  }
} catch (Exception $e) {
  $conn->rollback();
  echo $e->getMessage();
}

$stmt_usuarios->close();
$stmt_login->close();
$stmt_check_username->close();
$conn->close();

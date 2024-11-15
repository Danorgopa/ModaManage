<?php
// Iniciar la sesión
session_start();

// Conectar a la base de datos
$servername = "sql306.infinityfree.com";  // localhost
$username_db = "if0_37701389";       // usuario de la base de datos
$password_db = "mvOCfmjvomz";           // contraseña de la base de datos
$dbname = "if0_37701389_empresa_inventario";  // nombre de la base de datos

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Consultar la base de datos para verificar si el usuario existe en la tabla `login`
$sql = "SELECT login.password, usuarios.id, usuarios.nombre, usuarios.rol_id
        FROM login 
        JOIN usuarios ON login.usuario_id = usuarios.id 
        WHERE login.username = ?";

$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if (!$stmt) {
  die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // Si el usuario existe, verificamos la contraseña
  $user = $result->fetch_assoc();

  if (password_verify($password, $user['password'])) {
    // Contraseña correcta, iniciar sesión
    $_SESSION['username'] = $username;  // Almacenar el nombre de usuario en la sesión
    $_SESSION['user_id'] = $user['id']; // Almacenar el ID de usuario en la sesión (opcional)
    $_SESSION['nombre'] = $user['nombre']; // Almacenar el nombre del usuario (opcional)
    $_SESSION['rol_id'] = $user['rol_id']; // Almacenar el rol del usuario en la sesión

    header("Location: home.php");
    exit;
  } else {
    // Contraseña incorrecta, mostrar alerta
    echo "<script>
            alert('La contraseña es incorrecta.');
            window.location.href = 'index.html';
          </script>";
  }
} else {
  // Usuario no existe, mostrar alerta
  echo "<script>
          alert('El usuario no existe.');
          window.location.href = 'index.html';
        </script>";
}

$stmt->close();
$conn->close();
?>

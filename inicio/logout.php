<?php
// Iniciar la sesión
session_start();

// Destruir la sesión
session_destroy();

// Redirigir al inicio de sesión
header("Location: login.html");
exit;
?>

CREATE DATABASE IF NOT EXISTS empresa_inventario;
USE empresa_inventario;

-- 1. Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50),
    descripcion TEXT
);

-- 2. Tabla: marcas
CREATE TABLE IF NOT EXISTS marcas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

-- 3. Tabla: categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

-- 4. Tabla: proveedores (modificada para incluir 'email')
CREATE TABLE IF NOT EXISTS proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    email VARCHAR(100)
);

-- 5. Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    direccion VARCHAR(255),
    telefono VARCHAR(15),
    foto_perfil VARCHAR(255),
    fecha_nacimiento DATE,
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- 6. Tabla: login
CREATE TABLE IF NOT EXISTS login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    username VARCHAR(100),
    password VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 7. Tabla: historial_login
CREATE TABLE IF NOT EXISTS historial_login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    fecha_ingreso DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 8. Tabla: equipos
CREATE TABLE IF NOT EXISTS equipos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cantidad INT NOT NULL,
    proveedor_id INT,
    categoria_id INT,
    marca_id INT,
    precio DECIMAL(10,2),
    precio_total DECIMAL(10,2),
    caracteristicas TEXT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (marca_id) REFERENCES marcas(id)
);

-- 9. Tabla: tecnicos
CREATE TABLE IF NOT EXISTS tecnicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(15),
    correo VARCHAR(100),
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- 10. Tabla: estados_equipos
CREATE TABLE IF NOT EXISTS estados_equipos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

-- 11. Tabla: tipos_mantenimiento
CREATE TABLE IF NOT EXISTS tipos_mantenimiento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    frecuencia_mantenimiento INT
);

-- 12. Tabla: mantenimientos
CREATE TABLE IF NOT EXISTS mantenimientos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipo_id INT,
    tipo_mantenimiento_id INT,
    fecha_mantenimiento DATE,
    descripcion TEXT,
    tecnico_id INT,
    estado_id INT,
    ultimo_mantenimiento DATE,
    proximo_mantenimiento DATE,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (tipo_mantenimiento_id) REFERENCES tipos_mantenimiento(id),
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id),
    FOREIGN KEY (estado_id) REFERENCES estados_equipos(id)
);

-- 13. Tabla: fechas_productos
CREATE TABLE IF NOT EXISTS fechas_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_equipo_id INT,
    fecha_compra DATE,
    fecha_garantia DATE,
    fecha_vida_util DATE,
    codigo_unico_del_equipo VARCHAR(100),
    FOREIGN KEY (producto_equipo_id) REFERENCES equipos(id)
);

-- 14. Insertar datos en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES 
('admin', 'Administrador del sistema con acceso completo.'),
('inventarista', 'Responsable de la gestión del inventario.'),
('mantenimiento', 'Encargado de realizar el mantenimiento de equipos.'),
('reportes', 'Generador de informes y reportes del sistema.'),
('usuarios', 'Permite ver pero no modificar nada.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- 15. Insertar datos en la tabla estados_equipos
INSERT INTO estados_equipos (nombre) VALUES
('en mantenimiento'),
('libre'),
('en uso')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- 16. Insertar datos en la tabla categorías
INSERT INTO categorias (nombre) VALUES
('Computadoras'),
('Impresoras'),
('Equipos de red'),
('Televisores'),
('Sistemas de audio'),
('Proyectores'),
('Otros dispositivos electrónicos')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- 17. Insertar datos en la tabla marcas
INSERT INTO marcas (nombre) VALUES
('Samsung'),
('LG'),
('HP'),
('Lenovo'),
('Dell'),
('Epson'),
('Canon'),
('Xerox'),
('Asus'),
('Motorola')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- 18. Insertar datos en la tabla proveedores (con la columna email ya definida)
INSERT INTO proveedores (nombre, contacto, telefono, direccion, email) VALUES
('Tech Solutions S.A.S.', 'Juan Pérez', '3123456789', 'Carrera 10 #20-30', 'info@techsolutions.com'),
('Electro World Ltda.', 'María Gómez', '3219876543', 'Avenida 5 #10-20', 'contacto@electroworld.com'),
('Innovación Digital S.A.', 'Carlos Ruiz', '3101234567', 'Calle 25 #12-34', 'info@innovaciondigital.com'),
('Sistemas Avanzados S.A.S.', 'Ana Torres', '3174567890', 'Carrera 50 #30-40', 'info@sistemasavanzados.com'),
('Red de Soluciones Electrónicas', 'Luis Martínez', '3136547890', 'Avenida 15 #22-25', 'info@redsoluciones.com'),
('Dispositivos y Tecnología S.A.S.', 'Julián Morales', '3145678901', 'Calle 10 #15-20', 'info@dispositivosyt.com'),
('Soluciones Electrónicas de Colombia', 'Sofía León', '3112345678', 'Calle 30 #14-18', 'contacto@solucioneselectronicas.com'),
('Grupo Electrónico S.A.S.', 'Diego López', '3159876543', 'Carrera 12 #5-10', 'info@grupoelectronico.com'),
('Almacenamiento y Redes S.A.', 'Claudia Romero', '3128765432', 'Avenida 40 #30-45', 'info@almacenamiento.com'),
('Electrocomp S.A.S.', 'Esteban Rojas', '3107890123', 'Calle 22 #10-15', 'info@electrocomp.com')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), contacto = VALUES(contacto), telefono = VALUES(telefono), direccion = VALUES(direccion), email = VALUES(email);

-- 19. Insertar datos en la tabla tipos de mantenimiento
INSERT INTO tipos_mantenimiento (nombre, descripcion, frecuencia_mantenimiento) VALUES
('Preventivo', 'Mantenimiento regular para prevenir fallos', 30),
('Correctivo', 'Reparación de equipos dañados', NULL),
('Predictivo', 'Basado en la condición de los equipos', 90),
('De Emergencia', 'Realizado cuando hay un fallo crítico', NULL)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion), frecuencia_mantenimiento = VALUES(frecuencia_mantenimiento);

-- 20. Insertar un técnico de ejemplo
INSERT INTO tecnicos (nombre, apellido, telefono, correo, rol_id) VALUES
('Carlos', 'Pérez', '3001234567', 'carlos.perez@example.com', (SELECT id FROM roles WHERE nombre = 'mantenimiento'))
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido), telefono = VALUES(telefono), correo = VALUES(correo), rol_id = VALUES(rol_id);



# codigos para los roles en el registro
# admi                     = 109
# inventarista             = 619
# mantenimiento            = 226
# reporte                  = 610
# usuarios                 = " "


/*
-- 21. Consultar todos los usuarios
SELECT * FROM usuarios;


-- 22. Actualizar rol de usuario
SET SQL_SAFE_UPDATES = 0; -- Desactivar el modo seguro
UPDATE usuarios
SET rol_id = 1  -- Cambiar '1' por el ID del rol real si es diferente
WHERE email = 'admin11@gmail.com';
SET SQL_SAFE_UPDATES = 1; -- Reactivar el modo seguro
*/
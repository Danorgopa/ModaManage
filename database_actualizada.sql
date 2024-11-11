CREATE DATABASE IF NOT EXISTS empresa_inventario;
USE empresa_inventario;

-- 1. Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

-- 2. Tabla: empleados
CREATE TABLE IF NOT EXISTS empleados (
    cedula INT PRIMARY KEY,
    primer_nombre VARCHAR(100) NOT NULL,
    segundo_nombre VARCHAR(100),
    primer_apellido VARCHAR(100) NOT NULL,
    segundo_apellido VARCHAR(100),
    direccion VARCHAR(255),
    fecha_nacimiento DATE,
    licencia_conduccion VARCHAR(50),
    fecha_ingreso DATE,
    experiencia INT,
    edad INT,
    estado_civil VARCHAR(50),
    rh VARCHAR(5),
    eps VARCHAR(100),
    arl VARCHAR(100),
    estrato VARCHAR(10),
    profesion VARCHAR(100),
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- 3. Tabla: proveedor
CREATE TABLE IF NOT EXISTS proveedor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    correo VARCHAR(100) NOT NULL UNIQUE
);

-- 4. Tabla: inventario
CREATE TABLE IF NOT EXISTS inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2),
    fecha_ingreso DATE,
    proveedor_id INT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedor(id)
);

-- 5. Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    nombre VARCHAR(100),
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- 6. Tabla: login
CREATE TABLE IF NOT EXISTS login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 7. Tabla: historial_login
CREATE TABLE IF NOT EXISTS historial_login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    fecha_ingreso DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserción de datos en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Administrador con acceso total.'),
('inventarista', 'Gestión de inventarios.'),
('mantenimiento', 'Encargado de mantenimiento y soporte.'),
('reporte', 'Responsable de generar reportes.'),
('usuarios', 'Gestión de usuarios.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- Inserción de datos en la tabla proveedor
INSERT INTO proveedor (nombre, contacto, telefono, direccion, correo) VALUES
('Tech Solutions S.A.S.', 'Juan Pérez', '3123456789', 'Carrera 10 #20-30', 'info@techsolutions.com'),
('Electro World Ltda.', 'María Gómez', '3219876543', 'Avenida 5 #10-20', 'contacto@electroworld.com'),
('Innovación Digital S.A.', 'Carlos Ruiz', '3101234567', 'Calle 25 #12-34', 'info@innovaciondigital.com')
ON DUPLICATE KEY UPDATE
nombre = VALUES(nombre), contacto = VALUES(contacto), telefono = VALUES(telefono), 
direccion = VALUES(direccion), correo = VALUES(correo);

-- Inserción de datos en la tabla inventario
INSERT INTO inventario (nombre_producto, descripcion, cantidad, precio_unitario, fecha_ingreso, proveedor_id) VALUES
('Laptop Lenovo', 'Laptop de alto rendimiento con 16GB de RAM y 512GB SSD.', 10, 3500000.00, '2024-10-01', (SELECT id FROM proveedor WHERE nombre = 'Tech Solutions S.A.S.')),
('Impresora HP', 'Impresora multifuncional a color.', 5, 1200000.00, '2024-09-15', (SELECT id FROM proveedor WHERE nombre = 'Electro World Ltda.')),
('Router Cisco', 'Router de alto rendimiento para redes empresariales.', 8, 800000.00, '2024-08-20', (SELECT id FROM proveedor WHERE nombre = 'Innovación Digital S.A.'))
ON DUPLICATE KEY UPDATE
nombre_producto = VALUES(nombre_producto), descripcion = VALUES(descripcion), 
cantidad = VALUES(cantidad), precio_unitario = VALUES(precio_unitario), 
fecha_ingreso = VALUES(fecha_ingreso), proveedor_id = VALUES(proveedor_id);



# codigos para los roles en el registro
# admi                     = 109
# inventarista             = 619
# mantenimiento            = 226
# reporte                  = 610
# usuarios                 = " "

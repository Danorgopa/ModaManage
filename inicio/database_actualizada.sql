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

-- Inserción de datos en la tabla roles
INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Administrador con acceso total.'),
('inventarista', 'Gestión de inventarios.'),
('mantenimiento', 'Encargado de mantenimiento y soporte.'),
('reporte', 'Responsable de generar reportes.'),
('usuarios', 'Gestión de usuarios.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- Inserción de datos en la tabla empleados
INSERT INTO empleados (
    cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
    direccion, fecha_nacimiento, licencia_conduccion, fecha_ingreso, experiencia, 
    edad, estado_civil, rh, eps, arl, estrato, profesion, rol_id
) VALUES
(12345678, 'Juan', 'Carlos', 'Pérez', 'Gómez', 'Calle 10 #20-30', '1990-05-15', 'B1', 
 '2020-01-15', 5, 33, 'Casado', 'O+', 'Sanitas', 'Colpatria', '3', 'Ingeniero Industrial', 
 (SELECT id FROM roles WHERE nombre = 'admin')),

(87654321, 'Ana', NULL, 'Gómez', 'Rodríguez', 'Carrera 5 #15-20', '1985-09-20', 'B2', 
 '2018-07-10', 3, 38, 'Soltero', 'A-', 'Compensar', 'Sura', '2', 'Técnico en Logística', 
 (SELECT id FROM roles WHERE nombre = 'inventarista')),

(11223344, 'Carlos', 'Alberto', 'Ruiz', 'Martínez', 'Avenida 40 #50-60', '1992-11-10', NULL, 
 '2019-03-05', 2, 31, 'Unión Libre', 'AB+', 'Famisanar', 'Porvenir', '4', 
 'Tecnólogo en Electrónica', 
 (SELECT id FROM roles WHERE nombre = 'mantenimiento'))
ON DUPLICATE KEY UPDATE
primer_nombre = VALUES(primer_nombre), segundo_nombre = VALUES(segundo_nombre), 
primer_apellido = VALUES(primer_apellido), segundo_apellido = VALUES(segundo_apellido), 
direccion = VALUES(direccion), fecha_nacimiento = VALUES(fecha_nacimiento), 
licencia_conduccion = VALUES(licencia_conduccion), fecha_ingreso = VALUES(fecha_ingreso), 
experiencia = VALUES(experiencia), edad = VALUES(edad), estado_civil = VALUES(estado_civil), 
rh = VALUES(rh), eps = VALUES(eps), arl = VALUES(arl), estrato = VALUES(estrato), 
profesion = VALUES(profesion), rol_id = VALUES(rol_id);

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

-- Consultas de ejemplo
SELECT * FROM empleados;
SELECT * FROM proveedor;
SELECT * FROM inventario;

# codigos para los roles en el registro
# admi                     = 109
# inventarista             = 619
# mantenimiento            = 226
# reporte                  = 610
# usuarios                 = " "

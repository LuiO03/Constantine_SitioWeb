-- Eliminar las tablas si ya existen
DROP DATABASE IF EXISTS constantine_bd;
CREATE DATABASE constantine_bd;
USE constantine_bd;

-- Crear tabla de banners
CREATE TABLE banners (
    id_banner INT AUTO_INCREMENT PRIMARY KEY,
    pagina VARCHAR(50) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    titulo VARCHAR(100),
    enfasis VARCHAR(100),
    descripcion TEXT,
    orden INT DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE servicio (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(50) ,
    descripcion TEXT,
    detalle_1 TEXT,
    detalle_2 TEXT,
    detalle_3 TEXT,
    detalle_4 TEXT,
    detalle_5 TEXT,
    frase TEXT,
    imagen VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear tabla roles
CREATE TABLE roles (
    id_rol BIGINT AUTO_INCREMENT PRIMARY KEY,  -- Cambié BIGINT por INT para que sea consistente con el tipo de id_enlace
    rol_nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear tabla menu_enlaces
CREATE TABLE menu_enlaces (
    id_enlace INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

-- Crear tabla permisos
CREATE TABLE permisos (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_rol BIGINT NOT NULL,
    enlace_id INT NOT NULL,
    leer ENUM('hidden', 'visible') NOT NULL DEFAULT 'visible',
    crear ENUM('disabled', 'enabled') NOT NULL DEFAULT 'disabled',
    editar ENUM('disabled', 'enabled') NOT NULL DEFAULT 'disabled',
    eliminar ENUM('disabled', 'enabled') NOT NULL DEFAULT 'disabled',
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (enlace_id) REFERENCES menu_enlaces(id_enlace)
);

-- Crear tabla de usuarios
CREATE TABLE usuarios (
    id_usuario BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(8),
    nombres VARCHAR(255),
    apellidos VARCHAR(255),
    usuario VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    foto VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    id_rol BIGINT(20),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE SET NULL
);

CREATE TABLE contactos_negocio (
    hora_atencion VARCHAR(100) NOT NULL,
    direccion VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NOT NULL
);

CREATE TABLE redes_sociales (
    id_red INT AUTO_INCREMENT PRIMARY KEY,
    red VARCHAR(50) NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    enlace VARCHAR(255) NOT NULL,
    icono VARCHAR(50) NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE intentos_login (
    id_usuario INT NOT NULL,
    intentos INT DEFAULT 0,
    ultima_intento DATETIME,
    PRIMARY KEY (id_usuario)
);

CREATE TABLE locales (
    id_local INT AUTO_INCREMENT PRIMARY KEY,
    nombre_local VARCHAR(100) NOT NULL,
    direccion TEXT NOT NULL,
    horario TEXT NOT NULL,
    telefono VARCHAR(15),
    imagen VARCHAR(255),
    enlace VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE blogs (
    id_blog INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL,
    titulo_blog VARCHAR(100) NOT NULL,
    contenido TEXT, 
    imagen VARCHAR(255) 
);

CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de categorías
CREATE TABLE categorias(
    id_categoria INT(4) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_categoria VARCHAR(30) NOT NULL,
    imagen VARCHAR(255),
    ubicacion VARCHAR(30) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla de público
CREATE TABLE publico (
    id_publico INT(4) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_publico VARCHAR(50) NOT NULL,
    descripcion VARCHAR(50) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla de productos
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto VARCHAR(6) NOT NULL,
    nombre_producto VARCHAR(30) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    precio_venta DECIMAL(10, 2) NOT NULL,  -- Precio base del producto
    stock_total INT NOT NULL DEFAULT 0,    -- Stock total sumado de las variantes
    id_categoria INT,
    id_publico INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_publico) REFERENCES publico(id_publico) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE colores (
    id_color INT AUTO_INCREMENT PRIMARY KEY,
    nombre_color VARCHAR(50) NOT NULL,
    descripcion TEXT,
    codigo_color VARCHAR(7) NOT NULL  -- Código del color en formato hexadecimal (ejemplo: #FF5733)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tallas (
    id_talla INT AUTO_INCREMENT PRIMARY KEY,
    nombre_talla VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE productos_variantes (
    id_variante INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    id_color INT NOT NULL,
    id_talla INT NOT NULL,
    stock INT NOT NULL,  -- Stock específico por variante
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_color) REFERENCES colores(id_color) ON DELETE CASCADE,
    FOREIGN KEY (id_talla) REFERENCES tallas(id_talla) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE formulario_contacto (
    id_form_contacto INT AUTO_INCREMENT PRIMARY KEY, 
    nombres VARCHAR(25) NOT NULL,
    apellidos VARCHAR(25) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    terminos BOOLEAN NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE carrito (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario BIGINT(20) DEFAULT NULL,  -- Relacionado con el usuario
    id_variante INT NOT NULL,  -- Relacionado con la variante del producto
    cantidad INT NOT NULL DEFAULT 1,  -- Cantidad del producto
    precio_unitario DECIMAL(10, 2) NOT NULL, -- Precio unitario al agregar
    precio_total DECIMAL(10, 2) NOT NULL, -- Calculado como precio_unitario * cantidad
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_variante) REFERENCES productos_variantes(id_variante) ON DELETE CASCADE
);

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario BIGINT(20) NOT NULL,
    tipo_entrega ENUM('delivery', 'retirar') NOT NULL,
    direccion VARCHAR(255) DEFAULT NULL,  -- Dirección si es delivery
    id_local INT DEFAULT NULL,            -- ID del local si es retirar
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_local) REFERENCES locales(id_local) -- Si aplicable
);

CREATE TABLE pedidos_productos (
    id_pedido_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_variante INT NOT NULL,         -- Referencia a la variante de producto
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_variante) REFERENCES productos_variantes(id_variante) -- Tabla correcta
);

DELIMITER //
-- Trigger para recalcular el stock total al insertar una variante
CREATE TRIGGER after_insert_variante
AFTER INSERT ON productos_variantes
FOR EACH ROW
BEGIN
    UPDATE productos
    SET stock_total = (SELECT SUM(stock) FROM productos_variantes WHERE id_producto = NEW.id_producto)
    WHERE id_producto = NEW.id_producto;
END//

-- Trigger para recalcular el stock total al actualizar una variante
CREATE TRIGGER after_update_variante
AFTER UPDATE ON productos_variantes
FOR EACH ROW
BEGIN
    UPDATE productos
    SET stock_total = (SELECT SUM(stock) FROM productos_variantes WHERE id_producto = NEW.id_producto)
    WHERE id_producto = NEW.id_producto;
END//

-- Trigger para recalcular el stock total al eliminar una variante
CREATE TRIGGER after_delete_variante
AFTER DELETE ON productos_variantes
FOR EACH ROW
BEGIN
    UPDATE productos
    SET stock_total = (SELECT SUM(stock) FROM productos_variantes WHERE id_producto = OLD.id_producto)
    WHERE id_producto = OLD.id_producto;
END//
DELIMITER ;


INSERT INTO servicio (nombre_servicio, descripcion, detalle_1 , detalle_2 ,detalle_3 ,detalle_4 ,detalle_5, frase , imagen, fecha_creacion, fecha_actualizacion
) 

VALUES (
    'Estampados Personalizados',
    'Ofrecemos estampados personalizados en distintas prendas como camisetas, sudaderas, gorras y más. Puedes elegir entre vinilo textil, serigrafía, sublimación o bordado para crear tu prenda única.',
    'Prendas: camisetas, sudaderas, gorras, bolsos, delantales.', 
    'Tipos de estampado: vinilo textil, serigrafía, sublimación, bordado.',
    'Materiales de alta calidad y durabilidad. Ideal para regalos, eventos, uniformes y promociones de marca.',
    '',
    '',
    'Ideal para regalos, eventos, uniformes y promociones de marca.',
    'estampados_personalizados.jpg',
    '2024-01-01 10:00:00', 
    '2024-01-02 15:00:00'),
    
    (
    'Camisetas Deportivas Personalizadas',
    'Diseñamos y producimos camisetas deportivas personalizadas para equipos, clubes y eventos deportivos. Nuestros materiales transpirables garantizan comodidad y rendimiento en cualquier deporte.',
    'Materiales: dry-fit, poliéster transpirable.',
    'Personalización: nombres, números y logotipos de equipo.',
    'Opciones: camisetas de manga corta/larga, shorts, pantalones deportivos.',
    'Perfecto para equipos de fútbol, baloncesto, voleibol y más.',
    '',
    'Ideal para regalos, eventos, uniformes y promociones de marca.',
    'camisetas_deportivas.jpg',
    '2024-01-01 10:00:00',
    '2024-01-02 15:00:00'
);

-- Insertar datos en banners
INSERT INTO banners (pagina, imagen, titulo, enfasis, descripcion, orden, estado)
VALUES 
('inicio', 'inicio_banner1.jpg', 'Bienvenido a Constantine', 'Lo último en moda', 'Descubre nuestra nueva colección para todas las edades.', 1, 1),
('inicio', 'inicio_banner2.jpg', 'Ofertas Especiales', 'Descuentos únicos', 'Aprovecha nuestras ofertas por tiempo limitado.', 2, 1),
('inicio', 'inicio_banner3.jpg', 'Nuevas Tendencias', 'Primavera-Verano 2024', 'Encuentra tu estilo para la nueva temporada.', 3, 1),
('productos', 'banner_productos.jpg', 'Productos Exclusivos', 'Calidad garantizada', 'Explora nuestra amplia gama de productos de moda.', 1, 1),
('contactos', 'banner_contacto.jpg', 'Contáctanos', 'Estamos aquí para ti', 'No dudes en escribirnos para cualquier consulta o pedido.', 1, 1),
('nosotros', 'banner_nosotros.jpg', 'Conoce Constantine', 'Moda con propósito', 'Somos una marca comprometida con la moda sostenible.', 1, 1),
('servicios', 'banner_servicios.jpg', 'Nuestros Servicios', 'Atención personalizada', 'Ofrecemos servicios exclusivos para mejorar tu experiencia.', 1, 1),
('blog', 'banner_blog.jpg', 'Blog de Constantine', 'Inspírate con nuestras historias', 'Lee los últimos artículos sobre moda, tendencias y estilo de vida.', 1, 1);

-- Insertar roles
INSERT INTO roles (rol_nombre, descripcion, estado) VALUES
('Administrador', 'Administra el sistema', 1),
('Supervisor', 'Supervisa el sistema', 1),
('Almacenero', 'Controla el inventario', 1),
('Vendedor', 'Operada la tienda', 1),
('Cliente', 'Clientes en general', 1);

INSERT INTO menu_enlaces (id_enlace, nombre) VALUES
(1, 'inicio'),
(2, 'banners'),
(3, 'tienda'),
(4, 'publicos'),
(5, 'categorias'),
(6, 'productos'),
(7, 'info. negocio'),
(8, 'contactos'),
(9, 'locales'),
(10, 'servicios'),
(11, 'usuarios'),
(12, 'clientes'),
(13, 'roles'),
(14, 'blogs'),
(15, 'pedidos'),
(16, 'mensajes');


INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar)
VALUES
(1, 1, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 2, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 3, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 4, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 5, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 6, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 7, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 8, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 9, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 10, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 11, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 12, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 13, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 14, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 15, 'visible', 'enabled', 'enabled', 'enabled'),
(1, 16, 'visible', 'enabled', 'enabled', 'enabled');

-- Permisos para el Supervisor
INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar)
VALUES
(2, 1, 'visible', 'disabled', 'disabled', 'disabled'), -- Inicio
(2, 3, 'visible', 'disabled', 'disabled', 'disabled'), -- Tienda
(2, 4, 'visible', 'disabled', 'disabled', 'disabled'), -- Públicos
(2, 6, 'visible', 'disabled', 'disabled', 'disabled'), -- Productos
(2, 7, 'visible', 'disabled', 'disabled', 'disabled'), -- Info. Negocio
(2, 15, 'visible', 'disabled', 'disabled', 'disabled'); -- Pedidos

-- Permisos para el Almacenero
INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar)
VALUES
(3, 1, 'visible', 'disabled', 'disabled', 'disabled'), -- Inicio
(3, 4, 'visible', 'enabled', 'enabled', 'enabled'), -- Categorías
(3, 5, 'visible', 'enabled', 'enabled', 'enabled'), -- SubCategorías
(3, 6, 'visible', 'enabled', 'enabled', 'enabled'), -- Productos
(3, 3, 'visible', 'disabled', 'disabled', 'disabled'); -- Tienda

-- Permisos para el Vendedor
INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar)
VALUES
(4, 1, 'visible', 'disabled', 'disabled', 'disabled'), -- Inicio
(4, 15, 'visible', 'enabled', 'enabled', 'disabled'), -- Pedidos
(4, 12, 'visible', 'disabled', 'enabled', 'disabled'); -- Clientes

-- Permisos para el Cliente
INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar)
VALUES
(5, 1, 'visible', 'disabled', 'disabled', 'disabled'), -- Inicio
(5, 15, 'visible', 'enabled', 'disabled', 'disabled'); -- Pedidos


-- Insertar usuarios
INSERT INTO usuarios (id_usuario, dni, nombres, apellidos, usuario, correo, telefono, direccion, genero, foto, password, id_rol, fecha_creacion, fecha_ultimo_acceso, estado) VALUES
(1, '71649264', 'Eucladiana', 'Paucar Rosas', 'EuPaucar', 'eupaucar@gmail.com', '987654321', 'Calle Los Cedros 123', 'Femenino', 'roku.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 1, NOW(), NOW(), 1),
(2, '70098517', 'Luis', 'Quispe Osorio', 'LuiOsorio', 'luiosorio@gmail.com', '987654322', 'Av. Las Magnolias 456', 'Masculino', 'pikachu.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 3, NOW(), NOW(), 1);

-- Insertar clientes
INSERT INTO usuarios (id_usuario, dni, nombres, apellidos, usuario, correo, telefono, direccion, genero, foto, password, id_rol, fecha_creacion, fecha_ultimo_acceso, estado) VALUES
(3, '73211234', 'Mariana', 'Gonzales Paredes', 'MariGonzales', 'marianagonzales@example.com', '987654323', 'Jr. Los Pinos 789', 'Femenino', 'mari.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 5, NOW(), NOW(), 1),
(4, '74123345', 'Carlos', 'Sanchez Salazar', 'CarlosSanchez', 'carlos.sanchez@example.com', '987654324', 'Calle Ficus 321', 'Masculino', 'carlos.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 5, NOW(), NOW(), 1),
(5, '75123456', 'Ana', 'Cabrera Ruiz', 'AnaCabrera', 'anacabrera@example.com', '987654325', 'Av. Los Álamos 123', 'Femenino', 'ana.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 5, NOW(), NOW(), 1),
(6, '76234567', 'José', 'Martínez Flores', 'JoseMartinez', 'jose.martinez@example.com', '987654326', 'Jr. La Paz 456', 'Masculino', 'jose.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 5, NOW(), NOW(), 1),
(7, '77345678', 'Laura', 'Jiménez Vásquez', 'LauraJimenez', 'laura.jimenez@example.com', '987654327', 'Calle Los Naranjos 789', 'Femenino', 'laura.jpg', '$2y$10$lOX8LC69Oyqu4HA5a6Ehue4SH8I2j.8SEUb0XxHpvjbpo2j1OYaAC', 5, NOW(), NOW(), 1);

INSERT INTO contactos_negocio (hora_atencion, direccion, correo, telefono, celular) 
VALUES ('Lunes a Domingo: 8 am – 6 pm', 'James Sullivan, 127, colonia San Rafael', 'constantine@gmail.com', '+51 123 456 789', '+51 123 756 329');

INSERT INTO redes_sociales (red, titulo, descripcion, enlace, icono, estado) VALUES
('Facebook', 'Página De Facebook', 'Únete a nuestra comunidad y mantente informado con nuestras últimas noticias y eventos.', 'https://www.facebook.com/tu_pagina', 'ri-facebook-circle-fill', 1),
('TikTok', 'Cuenta De Tiktok', 'Descubre contenido divertido y creativo. Síguenos para más sorpresas.', 'https://www.tiktok.com/@tu_usuario', 'ri-tiktok-fill', 1),
('YouTube', 'Canal De Youtube', 'Mira nuestros videos y tutoriales exclusivos para aprender más sobre nuestros productos.', 'https://www.youtube.com/c/tu_canal', 'ri-youtube-fill', 0),
('Instagram', 'Cuenta De Instagram', 'Explora nuestras fotos y conoce más de nuestro día a día.', 'https://www.instagram.com/tu_usuario', 'ri-instagram-fill', 0),
('Twitter', 'Cuenta De Twitter', 'Entérate de nuestras últimas noticias y participa en las conversaciones.', 'https://twitter.com/tu_usuario', 'ri-twitter-fill', 0),
('WhatsApp', 'Cuenta de WhatsApp', 'Contáctanos directamente para soporte y preguntas a través de WhatsApp.', 'https://wa.me/tu_numero', 'ri-whatsapp-fill', 1);

INSERT INTO locales (nombre_local, direccion, horario, telefono,imagen, enlace) 
VALUES 
('Zona N°1', 'Av. 1ra Dirección', 'Lunes a Domingo: 8 am – 6 pm', '+51 123 456 789', 'local1.png','https://goo.gl/maps/enlace1'),
('Zona N°2', 'Calle 2, Distrito', 'Lunes a Viernes: 9 am – 5 pm', '+51 987 654 321','local1.png', 'https://goo.gl/maps/enlace2'),
('Zona N°3', 'Av. Central 100, Ciudad', 'Sábados y Domingos: 10 am – 4 pm', '+51 456 123 789','local1.png', 'https://goo.gl/maps/enlace3'),
('Zona N°4', 'Jr. Los Robles, Provincia', 'Todos los días: 7 am – 7 pm', '+51 321 654 987', 'local1.png','https://goo.gl/maps/enlace4');

INSERT INTO blogs (titulo_blog, categoria, contenido, imagen) 
VALUES
('Las Tendencias de Moda en 2024', 'Moda',
 'Las tendencias de moda para este año están marcadas por un regreso a los estilos de los 90s, con prendas de talle alto, chaquetas oversized y colores vibrantes. Además, los accesorios como los bolsos pequeños y las gafas grandes se están apoderando de las pasarelas. A medida que la moda se adapta al cambio climático, también se están viendo telas más sostenibles y amigables con el medio ambiente, como los tejidos reciclados y los materiales orgánicos. Por otro lado, la moda inclusiva está ganando fuerza, celebrando todas las formas y tamaños. No importa cuál sea tu estilo, las tendencias de 2024 permiten una mezcla entre lo clásico y lo vanguardista.',
 'blog1.png'),

('La Tecnología y su Impacto en la Industria de la Ropa', 'Tecnología',
 'La tecnología está transformando la industria de la moda de maneras sorprendentes. Desde los tejidos inteligentes que pueden regular la temperatura corporal hasta la implementación de la realidad aumentada en las tiendas, la tecnología está cambiando la manera en que experimentamos y compramos ropa. Los avances en la fabricación digital también están permitiendo la creación de prendas personalizadas, lo que mejora la experiencia del consumidor. Además, las plataformas en línea están utilizando inteligencia artificial para recomendar estilos basados en tus preferencias, lo que facilita la compra. Sin duda, la tecnología no solo está facilitando la producción de ropa, sino también personalizando la experiencia de compra para cada usuario.',
 'blog1.png'),

('Consejos de Estilo para el Trabajo', 'Estilo', 
 'Mantener un estilo profesional en el trabajo no significa renunciar a tu individualidad. Es importante encontrar un balance entre la formalidad y la comodidad, sobre todo cuando las tendencias actuales se enfocan en la mezcla de prendas formales e informales. Algunas piezas clave incluyen blazers entallados, pantalones de cintura alta y camisas de botones de materiales ligeros. También es esencial invertir en accesorios discretos pero sofisticados, como relojes o cinturones de cuero. No olvides que los colores neutros, como el blanco, negro, gris y azul marino, siempre son una apuesta segura. Combina tu estilo personal con el código de vestimenta de tu lugar de trabajo para mantener una apariencia profesional y elegante.',
 'blog1.png'),

('El Futuro de la Moda Sostenible', 'Sostenibilidad',
 'La moda sostenible es uno de los sectores de más rápido crecimiento en la industria textil, y su importancia solo aumentará en los próximos años. Las marcas están adoptando prácticas más ecológicas, como el uso de materiales reciclados y orgánicos, la reducción de desperdicios y la implementación de procesos de producción responsables. Además, se está dando un enfoque al comercio justo, con empresas que garantizan que los trabajadores sean tratados de manera ética y justa. La moda circular también está en auge, permitiendo que las prendas sean reutilizadas o recicladas al final de su vida útil. Sin duda, la sostenibilidad será un tema clave en el futuro de la moda, no solo para reducir el impacto ambiental, sino también para crear un modelo de negocio más ético y responsable.',
 'blog1.png'),

('Cómo Iniciar tu Propia Marca de Ropa', 'Emprendimiento',
 'Si alguna vez has soñado con crear tu propia marca de ropa, el momento es ahora. Empezar una marca de ropa requiere de planificación, creatividad y perseverancia. Primero, debes investigar el mercado para identificar una necesidad o nicho que tu marca pueda llenar. Luego, es importante desarrollar un concepto de marca sólido, que incluya el diseño, el logotipo, el nombre y el mensaje que deseas transmitir. También tendrás que gestionar la parte logística, como encontrar proveedores, establecer precios y planificar la distribución. Las redes sociales serán una herramienta clave para dar a conocer tu marca y conectarte con tu audiencia. Con paciencia y una visión clara, tu marca de ropa puede ser un éxito.',
 'blog1.png'),

('Las Nuevas Innovaciones en Tejidos Inteligentes', 'Innovación',
 'La moda está evolucionando rápidamente con la introducción de tejidos inteligentes. Estos materiales innovadores pueden adaptarse a las condiciones del entorno, como la temperatura o la humedad, y reaccionar en consecuencia. Por ejemplo, existen prendas que pueden cambiar de color según el entorno, lo que abre un mundo de posibilidades en la moda interactiva. Además, algunos tejidos están integrando tecnología de monitoreo de la salud, como sensores que miden la frecuencia cardíaca o la postura, lo que hace que la ropa no solo sea funcional, sino también útil para el bienestar personal. La industria de la moda está cada vez más cerca de fusionarse con la tecnología, creando prendas que son más que solo ropa, sino dispositivos interactivos.',
 'blog1.png');

-- Insertar datos en categorías
INSERT INTO categorias (id_categoria, nombre_categoria, imagen, ubicacion, fecha_creacion, fecha_actualizacion) VALUES
(1, 'Camisas', 'camisas.jpg', 'Planta Baja', '2024-01-01 10:00:00', '2024-01-02 15:00:00'),
(2, 'Pantalones', 'pantalones.jpg', 'Primer Piso', '2024-01-02 11:00:00', '2024-01-03 16:00:00'),
(3, 'Chaquetas', 'chaquetas.jpg', 'Segundo Piso', '2024-01-03 12:00:00', '2024-01-04 17:00:00'),
(4, 'Vestidos', 'vestidos.jpg', 'Tercer Piso', '2024-01-04 13:00:00', '2024-01-05 18:00:00'),
(5, 'Suéteres', 'sueteres.jpg', 'Planta Baja', '2024-01-05 14:00:00', '2024-01-06 19:00:00'),
(6, 'Shorts', 'shorts.jpg', 'Primer Piso', '2024-01-06 15:00:00', '2024-01-07 20:00:00'),
(7, 'Sport', 'sport.jpg', 'Segundo Piso', '2024-01-07 16:00:00', '2024-01-08 21:00:00'),
(8, 'Pijamas', 'pijamas.jpg', 'Tercer Piso', '2024-01-08 17:00:00', '2024-01-09 22:00:00'),
(9, 'Abrigos', 'abrigos.jpg', 'Planta Baja', '2024-01-09 18:00:00', '2024-01-10 23:00:00');

-- Insertar datos en público
INSERT INTO publico (id_publico, nombre_publico, descripcion, fecha_creacion) VALUES
(1, 'Caballeros', 'Ropa para hombres.', '2024-01-02 11:00:00'),
(2, 'Damas', 'Ropa para mujeres.', '2024-01-01 10:00:00'),
(3, 'Niños', 'Ropa para niñas.', '2024-01-03 12:00:00'),
(4, 'Niñas', 'Ropa para niños.', '2024-01-04 13:00:00');


INSERT INTO colores (nombre_color, descripcion, codigo_color) VALUES
('Rojo', 'Color rojo vibrante', '#FF0000'),
('Naranja', 'Color naranja brillante', '#FFA500'),
('Amarillo', 'Color amarillo brillante', '#FFFF00'),
('Verde', 'Color verde claro', '#008000'),
('Azul', 'Color azul marino', '#0000FF'),
('Índigo', 'Color índigo oscuro', '#4B0082'),
('Morado', 'Color morado intenso', '#800080'),
('Rosa', 'Color rosa suave', '#FFC0CB'),
('Blanco', 'Color blanco puro', '#FFFFFF'),
('Negro', 'Color negro clásico', '#000000'),
('Gris', 'Color gris oscuro', '#808080'),
('Beige', 'Color beige neutro', '#F5F5DC'),
('Marrón', 'Color marrón cálido', '#A52A2A'),
('Turquesa', 'Color turquesa vibrante', '#40E0D0'),
('Celeste', 'Color azul claro', '#ADD8E6'),
('Lavanda', 'Color lavanda suave', '#E6E6FA'),
('Verde Oliva', 'Color verde oliva oscuro', '#556B2F'),
('Vino', 'Color vino tinto profundo', '#800000'),
('Dorado', 'Color dorado brillante', '#FFD700'),
('Plateado', 'Color plateado metálico', '#C0C0C0'),
('Bronce', 'Color bronce metálico', '#CD7F32'),
('Café', 'Color café oscuro', '#6F4F28'),
('Mostaza', 'Color mostaza cálido', '#FFDB58'),
('Coral', 'Color coral claro', '#FF7F50'),
('Salmón', 'Color salmón suave', '#FA8072'),
('Lila', 'Color lila pastel', '#C8A2C8'),
('Púrpura', 'Color púrpura profundo', '#800080'),
('Fucsia', 'Color fucsia vibrante', '#FF00FF'),
('Cian', 'Color cian brillante', '#00FFFF'),
('Aqua', 'Color aqua fresco', '#00FFEF'),
('Azul Claro', 'Color azul claro suave', '#ADD8E6'),
('Verde Pastel', 'Color verde pastel suave', '#77DD77'),
('Rosa Claro', 'Color rosa claro pastel', '#FFB6C1'),
('Gris Claro', 'Color gris claro', '#D3D3D3'),
('Negro Azabache', 'Color negro intenso', '#0A0A0A'),
('Blanco Roto', 'Color blanco roto o hueso', '#F8F8FF'),
('Marfil', 'Color marfil suave', '#FFFFF0'),
('Lavanda Claro', 'Color lavanda suave', '#E6E6FA'),
('Menta', 'Color verde menta fresco', '#98FF98'),
('Limón', 'Color amarillo limón', '#FFF44F'),
('Azul Acero', 'Color azul acero metálico', '#4682B4'),
('Gris Pardo', 'Color gris pardo', '#BEBEBE'),
('Azul Turquesa', 'Color azul turquesa intenso', '#00CED1'),
('Siena', 'Color siena cálido', '#882D17'),
('Teal', 'Color verde azulado', '#008080'),
('Perla', 'Color perla suave y brillante', '#F0EAD6'),
('Fucsia Claro', 'Color fucsia suave', '#D72D72'),
('Ocre', 'Color ocre dorado', '#CC7722'),
('Rosa Chicle', 'Color rosa chicle brillante', '#FF1493'),
('Nude', 'Color nude o color piel', '#F2D2B6'),
('Salmón Claro', 'Color salmón claro pastel', '#FAEBD7'),
('Azul Pastel', 'Color azul pastel suave', '#AEC6CF'),
('Chocolate', 'Color marrón chocolate', '#3E2723'),
('Blanco Hueso', 'Color blanco hueso', '#F2F0EB'),
('Gris Pálido', 'Color gris pálido', '#DCDCDC'),
('Verde Esmeralda', 'Color verde esmeralda brillante', '#50C878'),
('Vino Claro', 'Color vino claro', '#8B0000'),
('Azul Cielo', 'Color azul cielo claro', '#87CEEB'),
('Rosa Pálido', 'Color rosa pálido', '#FADCD9'),
('Azul Marino', 'Color azul marino profundo', '#000080'),
('Marrón Claro', 'Color marrón claro', '#D2B48C'),
('Beige Claro', 'Color beige suave', '#F5F5DC');

INSERT INTO tallas (nombre_talla) VALUES
('S'),('M'),('L'),('XL'),('XXL');

INSERT INTO productos (id_producto, codigo_producto, nombre_producto, descripcion, imagen, precio_venta, id_categoria, id_publico, fecha_creacion, fecha_actualizacion) VALUES
(1, 'CAM001', 'Camisa de algodón', 'Camisa de algodón suave, ideal para el uso diario o eventos informales. Tela ligera y transpirable.', '1.webp', 20.00, 1, 1, '2024-01-10 10:00:00', '2024-01-10 10:00:00'),
(2, 'PAN001', 'Pantalón de mezclilla', 'Pantalón de mezclilla cómodo y resistente, ideal para el uso diario.', '2.webp', 30.00, 2, 2, '2024-01-11 11:00:00', '2024-01-11 11:00:00'),
(3, 'CHA001', 'Chaqueta impermeable', 'Chaqueta impermeable que protege en condiciones climáticas adversas.', '3.webp', 50.00, 3, 1, '2024-01-12 12:00:00', '2024-01-12 12:00:00'),
(4, 'SUE001', 'Suéter de lana', 'Suéter de lana suave y acogedora, perfecto para el clima frío.', '4.webp', 24.00, 5, 2, '2024-01-14 14:00:00', '2024-01-14 14:00:00'),
(5, 'SHT001', 'Shorts de tela', 'Shorts cómodos y ligeros, perfectos para los días calurosos.', '5.webp', 16.00, 6, 2, '2024-01-15 15:00:00', '2024-01-15 15:00:00'),
(6, 'SPO001', 'Camiseta deportiva', 'Camiseta deportiva que maximiza el rendimiento y ofrece ventilación.', '6.webp', 18.00, 7, 2, '2024-01-16 16:00:00', '2024-01-16 16:00:00'),
(7, 'PIJ001', 'Pijama cómoda', 'Pijama de algodón suave diseñada para una noche de descanso placentero.', '7.webp', 14.00, 8, 3, '2024-01-17 17:00:00', '2024-01-17 17:00:00'),
(8, 'ABR001', 'Abrigo de lana', 'Abrigo de lana elegante y clásico, ideal para los días fríos.', '8.webp', 70.00, 9, 1, '2024-01-18 18:00:00', '2024-01-18 18:00:00'),
(9, 'CAM002', 'Camisa con estampado', 'Camisa con estampado moderno, de algodón fresco y cómodo.', '11.webp', 22.00, 1, 1, '2024-01-19 10:00:00', '2024-01-19 10:00:00'),
(10, 'PAN002', 'Pantalón cargo', 'Pantalón cargo funcional con múltiples bolsillos, ideal para aventuras.', '22.webp', 34.00, 2, 2, '2024-01-20 11:00:00', '2024-01-20 11:00:00'),
(11, 'CHA002', 'Chaqueta acolchada', 'Chaqueta acolchada, ligera y térmica, ideal para climas fríos.', '33.webp', 55.00, 3, 1, '2024-01-21 12:00:00', '2024-01-21 12:00:00'),
(12, 'SUE002', 'Suéter casual', 'Suéter casual de algodón, cómodo y fácil de combinar.', '44.webp', 25.00, 5, 2, '2024-01-22 13:00:00', '2024-01-22 13:00:00'),
(13, 'SHT002', 'Shorts deportivos', 'Shorts deportivos, ligeros y elásticos para máxima movilidad.', '55.webp', 18.00, 6, 2, '2024-01-23 14:00:00', '2024-01-23 14:00:00'),
(14, 'SPO002', 'Camiseta sin mangas', 'Camiseta sin mangas para entrenamientos intensos y días calurosos.', '66.webp', 15.00, 7, 2, '2024-01-24 15:00:00', '2024-01-24 15:00:00'),
(15, 'PIJ002', 'Pijama de invierno', 'Pijama térmica de invierno para mantenerte cálido y cómodo.', '77.webp', 20.00, 8, 3, '2024-01-25 16:00:00', '2024-01-25 16:00:00'),
(16, 'ABR002', 'Abrigo acolchado', 'Abrigo acolchado resistente al agua, ideal para el invierno.', '88.webp', 75.00, 9, 1, '2024-01-26 17:00:00', '2024-01-26 17:00:00'),
(17, 'CAM003', 'Camisa formal', 'Camisa formal de algodón premium, perfecta para eventos importantes.', '111.webp', 28.00, 1, 1, '2024-01-27 18:00:00', '2024-01-27 18:00:00'),
(18, 'PAN003', 'Pantalón de vestir', 'Pantalón de vestir elegante con un corte moderno.', '222.webp', 40.00, 2, 2, '2024-01-28 19:00:00', '2024-01-28 19:00:00'),
(19, 'CHA003', 'Chaqueta casual', 'Chaqueta casual, ligera y perfecta para primavera.', '333.webp', 45.00, 3, 1, '2024-01-29 20:00:00', '2024-01-29 20:00:00'),
(20, 'SUE003', 'Suéter con cuello alto', 'Suéter con cuello alto, ideal para ocasiones formales y casuales.', '444.webp', 30.00, 5, 2, '2024-01-30 10:00:00', '2024-01-30 10:00:00'),
(21, 'SHT003', 'Shorts casuales', 'Shorts casuales con diseño relajado para uso diario.', '555.webp', 17.00, 6, 2, '2024-01-31 11:00:00', '2024-01-31 11:00:00'),
(22, 'SPO003', 'Camiseta ajustada', 'Camiseta ajustada con material que absorbe el sudor.', '666.webp', 20.00, 7, 2, '2024-02-01 12:00:00', '2024-02-01 12:00:00'),
(23, 'PIJ003', 'Abrigo ', 'Abrigo ligero y fresco ', '777.webp', 18.00, 9, 3, '2024-02-02 13:00:00', '2024-02-02 13:00:00'),
(24, 'ABR003', 'Abrigo largo', 'Abrigo largo elegante y cálido para ocasiones especiales.', '888.webp', 80.00, 9, 1, '2024-02-03 14:00:00', '2024-02-03 14:00:00'),
(25, 'CAM004', 'Camisa denim', 'Camisa denim clásica, ideal para un look casual.', '1111.webp', 26.00, 1, 1, '2024-02-04 15:00:00', '2024-02-04 15:00:00'),
(26, 'PAN004', 'Pantalón jogger', 'Pantalón jogger cómodo y moderno para uso diario.', '2222.webp', 38.00, 2, 2, '2024-02-05 16:00:00', '2024-02-05 16:00:00'),
(27, 'CHA004', 'Chaqueta bomber', 'Chaqueta bomber con diseño contemporáneo.', '3333.webp', 60.00, 3, 1, '2024-02-06 17:00:00', '2024-02-06 17:00:00'),
(28, 'SUE004', 'Suéter con capucha', 'Suéter con capucha para un estilo urbano.', '4444.webp', 35.00, 5, 2, '2024-02-07 18:00:00', '2024-02-07 18:00:00'),
(29, 'SHT004', 'Shorts de lino', 'Shorts frescos de lino para los días calurosos.', '5555.webp', 22.00, 6, 2, '2024-02-08 19:00:00', '2024-02-08 19:00:00'),
(30, 'SPO004', 'Camiseta de manga larga', 'Camiseta de manga larga para días frescos y cómodos.', '6666.webp', 21.00, 7, 2, '2024-02-09 20:00:00', '2024-02-09 20:00:00'),
(31, 'CAM005', 'Camisa hawaiana', 'Camisa de manga corta con estampado tropical ideal para el verano.', '7777.webp', 23.00, 1, 1, '2024-02-10 10:00:00', '2024-02-10 10:00:00'),
(32, 'PAN005', 'Pantalón deportivo', 'Pantalón deportivo para entrenamiento y actividades al aire libre.', '8888.webp', 28.00, 2, 2, '2024-02-11 11:00:00', '2024-02-11 11:00:00'),
(33, 'CHA005', 'Chaqueta de cuero', 'Chaqueta de cuero sintético con estilo moderno.', '11111.webp', 65.00, 3, 1, '2024-02-12 12:00:00', '2024-02-12 12:00:00'),
(34, 'SUE005', 'Suéter de cuello redondo', 'Suéter de cuello redondo, versátil para todas las estaciones.', '22222.webp', 27.00, 5, 2, '2024-02-13 13:00:00', '2024-02-13 13:00:00'),
(35, 'SHT005', 'Shorts de mezclilla', 'Shorts de mezclilla, perfectos para un look veraniego y cómodo.', '33333.webp', 24.00, 6, 2, '2024-02-14 14:00:00', '2024-02-14 14:00:00'),
(36, 'SPO005', 'Camiseta retro', 'Camiseta retro con estampado nostálgico de los años 90.', '44444.webp', 19.00, 7, 2, '2024-02-15 15:00:00', '2024-02-15 15:00:00'),
(37, 'PIJ004', 'Pijama infantil', 'Pijama para niños con personajes divertidos y confortables.', '55555.webp', 16.00, 8, 3, '2024-02-16 16:00:00', '2024-02-16 16:00:00'),
(38, 'ABR004', 'Abrigo impermeable', 'Abrigo impermeable, ideal para días lluviosos y fríos.', '66666.webp', 85.00, 9, 1, '2024-02-17 17:00:00', '2024-02-17 17:00:00'),
(39, 'CAM006', 'Camisa slim fit', 'Camisa slim fit elegante y moderna, perfecta para ocasiones formales.', '77777.webp', 30.00, 1, 1, '2024-02-18 18:00:00', '2024-02-18 18:00:00'),
(40, 'PAN006', 'Pantalón de lino', 'Pantalón de lino liviano y fresco para el verano.', '88888.webp', 35.00, 2, 2, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(41, 'ABR005', 'Abrigo con cuello de piel', 'Abrigo elegante con cuello de piel sintética, perfecto para invierno.', '111111.webp', 99.00, 9, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(42, 'ABR005', 'Abrigo con cuello de piel', 'Abrigo elegante con cuello de piel sintética, perfecto para invierno.', '222222.webp', 50.00, 9, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(43, 'ABR005', 'Abrigo con cuello de piel', 'Abrigo elegante con cuello de piel sintética, perfecto para invierno.', '333333.webp', 80.00, 9, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(44, 'VST001', 'Vestido casual ', 'Vestido ligero de algodón con estampado de flores, perfecto para paseos en verano.', '444444.webp', 50.00, 4, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(45, 'VST002', 'Vestido de verano ', '.Vestido ligero de algodón con estampado de flores o otros ', '555555.webp', 65.00, 4, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(46, 'VST003', 'Vestido playero ', 'Vestido fresco con diseño de rayas y colores vivos, perfecto para días soleados.', '666666.webp', 45.00, 4, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(47, 'PAN007', 'Pantalón casual', 'Pantalón  cómodo y resistente, ideal para el día a día.', 'P1.webp', 40.00, 2, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(48, 'PAN008', 'Pantalón de verano', 'Pantalón ligero de tela fresca con estampado floral, perfecto para climas cálidos.', 'P2.webp', 35.00, 2, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(49, 'PAN009', 'Pantalón deportivo', 'Pantalón elástico y transpirable, ideal para actividades deportivas o juegos.', 'P3.webp', 30.00, 2, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(50, 'SHT004', 'Short denim ', 'Short de mezclilla resistente con diseño moderno, perfecto para cualquier ocasión.', 'v3.webp', 30.00, 6, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(51, 'SHT005', 'Short estampado ', 'Short con estampado divertido y colores vibrantes, ideal para el verano.', 'v1.webp', 22.00, 6, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(52, 'SHT006', 'Short de encaje ', 'Short delicado con detalles de encaje, perfecto para eventos casuales y especiales.', 'v2.webp', 28.00, 6, 4, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(53, 'CMS001', 'Camisa casual ', 'Camisa de algodón cómoda y resistente, perfecta para el uso diario.', 'c1.webp', 35.00, 1, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(54, 'CMS002', 'Camisa  manga corta ', 'Camisa con estampados divertidos y coloridos, ideal para ocasiones informales.', 'c2.webp', 28.00, 1, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(55, 'CMS003', 'Camisa formal ', 'Camisa de corte elegante y diseño clásico, perfecta para eventos especiales.', 'c3.webp', 40.00, 1, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(56, 'CHQ001', 'Chaqueta de vestir', 'Chaqueta formal con diseño elegante, ideal para eventos importantes.', 'ch1.webp', 55.00, 3, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(57, 'CHQ002', 'Chaqueta deportiva', 'Chaqueta ligera y transpirable, perfecta para actividades al aire libre.', 'ch2.webp', 40.00, 3, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(58, 'PTN004', 'Pantalón casual ', 'Pantalón de algodón resistente y cómodo, ideal para el uso diario.', 'p1.webp', 38.00, 2, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(59, 'PTN005', 'Pantalón de vestir ', 'Pantalón con corte formal y diseño clásico, perfecto para eventos importantes.', 'p2.webp', 45.00, 2, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00'),
(60, 'PTN006', 'Pantalón deportivo ', 'Pantalón elástico y ligero, ideal para actividades físicas y juegos al aire libre.', 'p3.webp', 30.00, 2, 3, '2024-02-19 19:00:00', '2024-02-19 19:00:00');

INSERT INTO formulario_contacto (nombres, apellidos, telefono, correo, mensaje, terminos)
VALUES 
('Juan', 'Pérez', '987654321', 'juan.perez@email.com', 'Estoy interesado en obtener más información sobre sus servicios.', TRUE),
('Ana', 'González', '923456789', 'ana.gonzalez@email.com', 'Quiero saber si tienen promociones para nuevos clientes.', FALSE),
('Carlos', 'Rodríguez', '912345678', 'carlos.rodriguez@email.com', 'Me gustaría contratar un servicio para mi empresa.', TRUE),
('María', 'Lopez', '931234567', 'maria.lopez@email.com', '¿Tienen soporte en línea? Necesito ayuda con un pedido.', TRUE),
('Pedro', 'Martínez', '950123456', 'pedro.martinez@email.com', 'El sitio web me parece excelente, ¿hay descuentos en las compras al por mayor?', FALSE);


INSERT INTO productos_variantes (id_producto, id_color, id_talla, stock) VALUES
('1', '13', '4', '20'),
('1', '13', '1', '20'),
('1', '13', '2', '20'),
('1', '13', '3', '20'),
('1', '14', '3', '20'),
('1', '10', '5', '20'),
('2', '14', '1', '15'),
('2', '16', '2', '10'),
('3', '10', '4', '18'),
('3', '11', '5', '25'),
('4', '7', '1', '12'),
('4', '9', '3', '17'),
('5', '20', '1', '10'),
('5', '21', '2', '12'),
('6', '18', '4', '20'),
('6', '19', '5', '30'),
('7', '12', '1', '25'),
('7', '14', '3', '15'),
('8', '5', '4', '20'),
('8', '6', '5', '22'),
('9', '3', '2', '14'),
('9', '4', '3', '16'),
('10', '25', '1', '18'),
('10', '27', '3', '22'),
('11', '8', '1', '15'),
('11', '9', '2', '12'),
('12', '14', '3', '17'),
('12', '15', '4', '20'),
('13', '5', '5', '25'),
('13', '6', '1', '18'),
('14', '18', '2', '14'),
('14', '19', '3', '22'),
('15', '7', '4', '20'),
('15', '9', '5', '25'),
('16', '20', '1', '30'),
('16', '21', '2', '10'),
('17', '11', '3', '15'),
('17', '12', '4', '18'),
('18', '13', '5', '20'),
('18', '25', '1', '22'),
('19', '14', '2', '17'),
('19', '16', '3', '14'),
('20', '5', '4', '19'),
('20', '6', '5', '20'),
('21', '10', '1', '16'),
('21', '11', '2', '14'),
('22', '19', '3', '20'),
('22', '20', '4', '12'),
('23', '3', '5', '17'),
('23', '4', '1', '10'),
('24', '15', '2', '25'),
('24', '16', '3', '18'),
('25', '8', '4', '20'),
('25', '9', '5', '22'),
('26', '25', '1', '15'),
('26', '26', '2', '18'),
('27', '13', '3', '20'),
('27', '14', '4', '12'),
('28', '5', '5', '25'),
('28', '6', '1', '14'),
('29', '7', '2', '20'),
('29', '8', '3', '30'),
('30', '9', '4', '18'),
('30', '10', '5', '15'),
('31', '11', '1', '12'),
('31', '12', '2', '10'),
('32', '14', '3', '20'),
('32', '16', '4', '25'),
('33', '18', '5', '18'),
('33', '19', '1', '15'),
('34', '20', '2', '22'),
('34', '21', '3', '14'),
('35', '25', '4', '20'),
('35', '26', '5', '19'),
('36', '13', '1', '17'),
('36', '14', '2', '22'),
('37', '5', '3', '18'),
('37', '6', '4', '20'),
('38', '7', '5', '14'),
('38', '9', '1', '10'),
('39', '3', '2', '18'),
('39', '4', '3', '25'),
('40', '15', '4', '20'),
('40', '16', '5', '18'),
(41, 1, 1, 5),  -- Abrigo con cuello de piel, color 1 (Ej. Negro), talla 1 (S)
(41, 1, 2, 4),  -- Abrigo con cuello de piel, color 1 (Ej. Negro), talla 2 (M)
(41, 1, 3, 0),  -- Abrigo con cuello de piel, color 1 (Ej. Negro), talla 3 (L)
(42, 2, 1, 6),  -- Abrigo con cuello de piel, color 2 (Ej. Gris), talla 1 (S)
(42, 2, 2, 3),  -- Abrigo con cuello de piel, color 2 (Ej. Gris), talla 2 (M)
(42, 2, 3, 0),  -- Abrigo con cuello de piel, color 2 (Ej. Gris), talla 3 (L)
(43, 3, 1, 7),  -- Abrigo con cuello de piel, color 3 (Ej. Azul), talla 1 (S)
(43, 3, 2, 2),  -- Abrigo con cuello de piel, color 3 (Ej. Azul), talla 2 (M)
(43, 3, 3, 1),  -- Abrigo con cuello de piel, color 3 (Ej. Azul), talla 3 (L)
(44, 4, 1, 10), -- Vestido casual, color 4 (Ej. Rojo), talla 1 (S)
(44, 4, 2, 5),  -- Vestido casual, color 4 (Ej. Rojo), talla 2 (M)
(44, 4, 3, 0),  -- Vestido casual, color 4 (Ej. Rojo), talla 3 (L)
(45, 5, 1, 8),  -- Vestido de verano, color 5 (Ej. Rosa), talla 1 (S)
(45, 5, 2, 4),  -- Vestido de verano, color 5 (Ej. Rosa), talla 2 (M)
(45, 5, 3, 0),  -- Vestido de verano, color 5 (Ej. Rosa), talla 3 (L)
(46, 6, 1, 9),  -- Vestido playero, color 6 (Ej. Amarillo), talla 1 (S)
(46, 6, 2, 6),  -- Vestido playero, color 6 (Ej. Amarillo), talla 2 (M)
(46, 6, 3, 2),  -- Vestido playero, color 6 (Ej. Amarillo), talla 3 (L)
(47, 7, 1, 3),  -- Pantalón casual, color 7 (Ej. Azul), talla 1 (S)
(47, 7, 2, 5),  -- Pantalón casual, color 7 (Ej. Azul), talla 2 (M)
(47, 7, 3, 2),  -- Pantalón casual, color 7 (Ej. Azul), talla 3 (L)
(48, 8, 1, 10), -- Pantalón de verano, color 8 (Ej. Blanco), talla 1 (S)
(48, 8, 2, 7),  -- Pantalón de verano, color 8 (Ej. Blanco), talla 2 (M)
(48, 8, 3, 4),  -- Pantalón de verano, color 8 (Ej. Blanco), talla 3 (L)
(49, 9, 1, 5),  -- Pantalón deportivo, color 9 (Ej. Gris oscuro), talla 1 (S)
(49, 9, 2, 8),  -- Pantalón deportivo, color 9 (Ej. Gris oscuro), talla 2 (M)
(49, 9, 3, 4),  -- Pantalón deportivo, color 9 (Ej. Gris oscuro), talla 3 (L)
(50, 10, 1, 6), -- Short denim, color 10 (Ej. Azul claro), talla 1 (S)
(50, 10, 2, 5), -- Short denim, color 10 (Ej. Azul claro), talla 2 (M)
(50, 10, 3, 2), -- Short denim, color 10 (Ej. Azul claro), talla 3 (L)
(51, 11, 1, 8), -- Short estampado, color 11 (Ej. Naranja), talla 1 (S)
(51, 11, 2, 6), -- Short estampado, color 11 (Ej. Naranja), talla 2 (M)
(51, 11, 3, 4), -- Short estampado, color 11 (Ej. Naranja), talla 3 (L)
(52, 12, 1, 7), -- Short de encaje, color 12 (Ej. Blanco), talla 1 (S)
(52, 12, 2, 5), -- Short de encaje, color 12 (Ej. Blanco), talla 2 (M)
(52, 12, 3, 3), -- Short de encaje, color 12 (Ej. Blanco), talla 3 (L)
(53, 13, 1, 5), -- Camisa casual, color 13 (Ej. Verde), talla 1 (S)
(53, 13, 2, 6), -- Camisa casual, color 13 (Ej. Verde), talla 2 (M)
(53, 13, 3, 4), -- Camisa casual, color 13 (Ej. Verde), talla 3 (L)
(54, 14, 1, 9), -- Camisa manga corta, color 14 (Ej. Amarillo), talla 1 (S)
(54, 14, 2, 7), -- Camisa manga corta, color 14 (Ej. Amarillo), talla 2 (M)
(54, 14, 3, 5), -- Camisa manga corta, color 14 (Ej. Amarillo), talla 3 (L)
(55, 15, 1, 6), -- Camisa formal, color 15 (Ej. Blanco), talla 1 (S)
(55, 15, 2, 4), -- Camisa formal, color 15 (Ej. Blanco), talla 2 (M)
(55, 15, 3, 2), -- Camisa formal, color 15 (Ej. Blanco), talla 3 (L)
(56, 16, 1, 5), -- Chaqueta de vestir, color 16 (Ej. Negro), talla 1 (S)
(56, 16, 2, 6), -- Chaqueta de vestir, color 16 (Ej. Negro), talla 2 (M)
(56, 16, 3, 4), -- Chaqueta de vestir, color 16 (Ej. Negro), talla 3 (L)
(57, 17, 1, 8), -- Chaqueta deportiva, color 17 (Ej. Gris claro), talla 1 (S)
(57, 17, 2, 7), -- Chaqueta deportiva, color 17 (Ej. Gris claro), talla 2 (M)
(57, 17, 3, 5), -- Chaqueta deportiva, color 17 (Ej. Gris claro), talla 3 (L)
(58, 18, 1, 4), -- Pantalón casual, color 18 (Ej. Beige), talla 1 (S)
(58, 18, 2, 6), -- Pantalón casual, color 18 (Ej. Beige), talla 2 (M)
(58, 18, 3, 3), -- Pantalón casual, color 18 (Ej. Beige), talla 3 (L)
(59, 19, 1, 7), -- Pantalón de vestir, color 19 (Ej. Negro), talla 1 (S)
(59, 19, 2, 5), -- Pantalón de vestir, color 19 (Ej. Negro), talla 2 (M)
(59, 19, 3, 4), -- Pantalón de vestir, color 19 (Ej. Negro), talla 3 (L)
(60, 20, 1, 10), -- Pantalón deportivo, color 20 (Ej. Azul), talla 1 (S)
(60, 20, 2, 8), -- Pantalón deportivo, color 20 (Ej. Azul), talla 2 (M)
(60, 20, 3, 6); -- Pantalón deportivo, color 20 (Ej. Azul), talla 3 (L)


-- ==========================================
-- BASE DE DATOS ECOLIM
-- ==========================================

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabla de tipos de residuo
CREATE TABLE IF NOT EXISTS tipos_residuo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- Tabla de residuos
CREATE TABLE IF NOT EXISTS residuos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_residuo_id INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    ubicacion VARCHAR(150),
    observaciones TEXT,
    sincronizado TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_residuos_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_residuos_tipo
        FOREIGN KEY (tipo_residuo_id)
        REFERENCES tipos_residuo(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- ==========================================
-- DATOS INICIALES
-- ==========================================

INSERT IGNORE INTO usuarios (usuario, password)
VALUES
('admin', '1234'),
('trabajador', '1234');

INSERT IGNORE INTO tipos_residuo (nombre)
VALUES
('Orgánico'),
('Reciclable'),
('Inorgánico'),
('Peligroso'),
('No aprovechable');
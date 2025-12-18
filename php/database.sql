-- ==========================================
-- Crear base de datos (modifica el nombre)
-- ==========================================
CREATE DATABASE IF NOT EXISTS UO295180_DB;

USE UO295180_DB;

-- ==========================================
-- Tabla: users
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profesion VARCHAR(255),
    edad INT,
    genero BOOLEAN,
    pericia FLOAT
);

-- ==========================================
-- Tabla: results
-- ==========================================
CREATE TABLE IF NOT EXISTS results (
    resultado_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    dispositivo VARCHAR(255),
    tiempo FLOAT,
    completado BOOLEAN,
    comentarios TEXT,
    valoracion INT,
    resultados TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- ==========================================
-- Tabla: observaciones_facilitador
-- ==========================================
CREATE TABLE IF NOT EXISTS observaciones_facilitador (
    id_observacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    comentarios TEXT,
    FOREIGN KEY (id_usuario) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS medidas_usuario (
    id_medida INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    altura DECIMAL(5,2) NOT NULL,
    edad INT NOT NULL,
    sexo ENUM('hombre', 'mujer') NOT NULL,
    actividad_semanal TINYINT UNSIGNED NOT NULL DEFAULT 3,
    intensidad ENUM('baja', 'media', 'alta') NOT NULL DEFAULT 'media',
    objetivo ENUM('perder_grasa', 'mantener_peso', 'ganar_masa') NOT NULL DEFAULT 'mantener_peso',
    calorias_recomendadas INT NOT NULL DEFAULT 0,
    agua_litros DECIMAL(4,2) NOT NULL DEFAULT 0,
    proteinas DECIMAL(6,2) NOT NULL DEFAULT 0,
    carbohidratos DECIMAL(6,2) NOT NULL DEFAULT 0,
    grasas DECIMAL(6,2) NOT NULL DEFAULT 0,
    imc DECIMAL(5,2) NOT NULL DEFAULT 0,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_medidas_usuario (id_usuario),
    CONSTRAINT fk_medidas_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

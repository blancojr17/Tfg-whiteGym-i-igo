ALTER TABLE medidas_usuario
    ADD COLUMN actividad_semanal TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER sexo,
    ADD COLUMN intensidad ENUM('baja', 'media', 'alta') NOT NULL DEFAULT 'media' AFTER actividad_semanal,
    ADD COLUMN objetivo ENUM('perder_grasa', 'mantener_peso', 'ganar_masa') NOT NULL DEFAULT 'mantener_peso' AFTER intensidad,
    ADD COLUMN calorias_recomendadas INT NOT NULL DEFAULT 0 AFTER objetivo,
    ADD COLUMN agua_litros DECIMAL(4,2) NOT NULL DEFAULT 0 AFTER calorias_recomendadas,
    ADD COLUMN proteinas DECIMAL(6,2) NOT NULL DEFAULT 0 AFTER agua_litros,
    ADD COLUMN carbohidratos DECIMAL(6,2) NOT NULL DEFAULT 0 AFTER proteinas,
    ADD COLUMN grasas DECIMAL(6,2) NOT NULL DEFAULT 0 AFTER carbohidratos,
    ADD COLUMN imc DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER grasas;

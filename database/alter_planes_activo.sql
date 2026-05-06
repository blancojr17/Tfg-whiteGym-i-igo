-- Ejecutar una sola vez en gym_tfg si la columna activo aún no existe.
ALTER TABLE planes
ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;

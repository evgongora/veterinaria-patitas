-- Bases ya creadas sin columna ICONO: ejecutar una vez.
ALTER TABLE SERVICIO ADD COLUMN ICONO VARCHAR(32) NULL AFTER DURACION;

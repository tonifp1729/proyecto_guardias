-- Autores: Antonio M. Figueroa Pinilla y Leandro J. Paniagua Balbuena
-- Inserción de tablas:
CREATE TABLE Rol (
    id CHAR(1) NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    descripcion VARCHAR(255) NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Usuario (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    correo VARCHAR(255) NOT NULL,
    nombre VARCHAR(90) NOT NULL,
    apellidos VARCHAR(255) NOT NULL,
    fecha_ingreso DATETIME NOT NULL,
    id_Rol CHAR(1) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_Rol) REFERENCES Rol(id)
);

CREATE TABLE Motivo (
    id TINYINT UNSIGNED AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255) NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Curso (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    anio_academico CHAR(5) NOT NULL,
    estado CHAR(1) NOT NULL DEFAULT 'P',
    PRIMARY KEY (id),
    CONSTRAINT chk_estado_curso CHECK (estado IN ('A', 'P', 'F'));
);

CREATE TABLE Solicitud (
    id_Usuario INT UNSIGNED NOT NULL,
    fecha_presentacion DATE NOT NULL,
    num TINYINT UNSIGNED NOT NULL,
    id_Curso INT UNSIGNED NOT NULL,
    id_Motivo TINYINT UNSIGNED NOT NULL,
    fecha_inicio_ausencia DATE NOT NULL,
    fecha_fin_ausencia DATE NOT NULL, 
    estado CHAR(1) NOT NULL,
    descripcion_solicitud VARCHAR(500) NULL, 
    comentario_material VARCHAR(1000) NULL,
    PRIMARY KEY (id_Usuario, fecha_presentacion, num),
    FOREIGN KEY (id_Usuario) REFERENCES Usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (id_Motivo) REFERENCES Motivo(id) ON DELETE CASCADE,
    FOREIGN KEY (id_Curso) REFERENCES Curso(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Hora (
    id_Usuario_Solicitud INT UNSIGNED NOT NULL,
    fecha_presentacion DATE NOT NULL,
    num TINYINT UNSIGNED NOT NULL,
    num_hora INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_Usuario_Solicitud, fecha_presentacion, num, num_hora),
    FOREIGN KEY (id_Usuario_Solicitud, fecha_presentacion, num) REFERENCES Solicitud(id_Usuario, fecha_presentacion, num) ON DELETE CASCADE
);

CREATE TABLE Archivo (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    id_Usuario_Solicitud INT UNSIGNED NOT NULL,
    fecha_presentacion DATE NOT NULL,
    num TINYINT UNSIGNED NOT NULL,
    nombre_original VARCHAR(255),
    nombre_generado VARCHAR(255),
    tipo_archivo VARCHAR(10),
    ruta_archivo VARCHAR(4096),
    PRIMARY KEY (id),
    FOREIGN KEY (id_Usuario_Solicitud, fecha_presentacion, num) REFERENCES Solicitud(id_Usuario, fecha_presentacion, num) ON DELETE CASCADE
);

-- Inserciones obligatorias de usuario administrador, roles y motivos
INSERT INTO Rol (id, nombre, descripcion) VALUES
('A', 'Administrador', 'Usuario con privilegios administrativos'),
('M', 'Moderador', 'Usuario con privilegios de moderación'),
('C', 'Usuario', 'Usuario común del sistema');

INSERT INTO Motivo (nombre, descripcion) VALUES
('enfermedad', 'Ausencia por enfermedad o baja médica'),
('problema-familiar', 'Ausencia por problemas familiares'),
('visita-medica', 'Ausencia por visita médica'),
('cambio-domicilio', 'Ausencia por cambio de domicilio'),
('dia-sin-sueldo', 'Ausencia por día sin sueldo'),
('formacion-reunion', 'Ausencia por formación o reunión'),
('experiencias', 'Ausencia por experiencias o actividades relacionadas'),
('actividad-extraescolar', 'Ausencia por actividades extraescolares o complementarias'),
('baja-maternidad-paternidad', 'Ausencia por baja por maternidad o paternidad'),
('matrimonio', 'Ausencia por matrimonio'),
('asuntos-propios', 'Ausencia por asuntos propios'),
('otros', 'Otro tipo de ausencia no especificado');

INSERT INTO Usuario (correo, nombre, apellidos,fecha_ingreso, id_Rol) VALUES 
('dirsecundaria.guadalupe@fundacionloyola.es', 'Director', 'Secundaria', '2025-05-16 18:17:16', 'A'),
('antoniomanuelfigueroapinilla.guadalupe@alumnado.fundacionloyola.net', 'Great Master', 'Anonio Mamue', '2025-05-16 18:17:16', 'A');
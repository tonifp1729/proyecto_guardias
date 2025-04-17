CREATE TABLE Roles (
    idRol CHAR(1) NOT NULL,
    nombreRol VARCHAR(255) NOT NULL,
    descripcion TEXT,
    PRIMARY KEY (idRol)
);

CREATE TABLE Etapas (
    idEtapa CHAR(1) NOT NULL,
    nombreEtapa VARCHAR(255) NOT NULL,
    descripcion TEXT,
    PRIMARY KEY (idEtapa)
);

CREATE TABLE Usuarios (
    idUsuario INT AUTO_INCREMENT,
    correo VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    apellidos VARCHAR(255) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol CHAR(1) NOT NULL,
    PRIMARY KEY (idUsuario),
    FOREIGN KEY (rol) REFERENCES Roles(idRol)
);

CREATE TABLE usuarios_etapas (
    idUsuario INT NOT NULL,
    idEtapa CHAR(1) NOT NULL,
    PRIMARY KEY (idUsuario, idEtapa),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idEtapa) REFERENCES Etapas(idEtapa) ON DELETE CASCADE
);

CREATE TABLE Cursos (
    idCurso INT AUTO_INCREMENT,
    anoAcademico CHAR(9) NOT NULL UNIQUE,
    fechaInicio DATE NOT NULL,
    fechaFinalizacion DATE NOT NULL,
    PRIMARY KEY (idCurso)
);

CREATE TABLE Motivos (
    idMotivo INT AUTO_INCREMENT,
    nombreMotivo VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NULL,
    PRIMARY KEY (idMotivo)
);

CREATE TABLE Solicitudes (
    idUsuarioSolicitante INT NOT NULL,
    fechaInicioAusencia DATE NOT NULL,
    fechaFinAusencia DATE NOT NULL,
    horasAusencia INT NULL,
    motivo INT NOT NULL,
    descripcionMotivo VARCHAR(255) NULL,
    estado BIT NULL,
    idCurso INT NOT NULL,
    PRIMARY KEY (idUsuarioSolicitante, fechaInicioAusencia),
    FOREIGN KEY (idUsuarioSolicitante) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (motivo) REFERENCES Motivos(idMotivo),
    FOREIGN KEY (idCurso) REFERENCES Cursos(idCurso) ON DELETE CASCADE
);

CREATE TABLE Archivos (
    idArchivo INT AUTO_INCREMENT,
    idUsuarioArchiva INT NOT NULL,
    fechaInicioAusencia DATE NOT NULL,
    nombreOriginal VARCHAR(255) NOT NULL,
    nombreGenerado VARCHAR(255) NOT NULL, 
    tipoArchivo VARCHAR(10) NOT NULL,
    rutaArchivo VARCHAR(255) NOT NULL,
    PRIMARY KEY (idArchivo),
    FOREIGN KEY (idUsuarioArchiva, fechaInicioAusencia) REFERENCES Solicitudes(idUsuarioSolicitante, fechaInicioAusencia) 
        ON DELETE CASCADE
);

CREATE TABLE Historico_Moderacion (

);

INSERT INTO Roles (idRol, nombreRol, descripcion) VALUES
('A', 'Administrador', 'Usuario con privilegios administrativos'),
('M', 'Moderador', 'Usuario con privilegios de moderación'),
('U', 'Usuario', 'Usuario común del sistema');

INSERT INTO Etapas (idEtapa, nombreEtapa, descripcion) VALUES
('I', 'Infantil', 'Departamento de educación infantil'),
('P', 'Primaria', 'Departamento de educación primaria'),
('S', 'Secundaria', 'Departamento de educación secundaria'),
('B', 'Bachillerato', 'Departamento de bachillerato'),
('F', 'Formación Profesional', 'Departamento de formación profesional');

INSERT INTO Motivos (nombreMotivo, descripcion) VALUES
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

INSERT INTO Usuarios (correo, nombre, apellidos, contrasena, rol) 
VALUES ('dirsecundaria.guadalupe@fundacionloyola.es', 'Director', 'Secundaria', SHA2('mi_contrasena_segura', 256), 'A');

INSERT INTO usuarios_etapas (idUsuario, idEtapa) VALUES
(1, 'S'),
(1, 'F');

--CONSULTAS PARA ELIMINAR LAS TABLAS-------------------------------------------------------------------------------------------

DROP TABLE historico_gestiones;
DROP TABLE Archivos;

DROP TABLE Solicitudes;
DROP TABLE usuarios_etapas;

DROP TABLE Motivos;
DROP TABLE Cursos;
DROP TABLE Usuarios;
DROP TABLE Etapas;
DROP TABLE Roles;
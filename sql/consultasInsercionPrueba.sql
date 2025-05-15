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

INSERT INTO Usuarios (correo, nombre, apellidos, contrasena, rol) 
VALUES ('dirsecundaria.guadalupe@fundacionloyola.es', 'Director', 'Secundaria', SHA2('mi_contrasena_segura', 256), 'A');

--CONSULTAS PARA ELIMINAR LAS TABLAS-------------------------------------------------------------------------------------------

DROP TABLE Archivo;
DROP TABLE Hora;
DROP TABLE Solicitud;
DROP TABLE Motivo;
DROP TABLE Curso;
DROP TABLE Usuario;
DROP TABLE Rol;
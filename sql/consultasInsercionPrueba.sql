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

INSERT INTO Usuario (correo, nombre, apellidos,fecha_ingreso, id_Rol) 
VALUES ('dirsecundaria.guadalupe@fundacionloyola.es', 'Director', 'Secundaria', '2025-05-16 18:17:16', 'A');


--INSERCIONES DE PRUEBA--------------------------------------------------------------------------------------------------------

INSERT INTO Curso (fecha_inicio, fecha_fin, anio_academico) VALUES
('2022-09-12', '2023-06-23', '22/23'),
('2023-09-11', '2024-06-21', '23/24');

INSERT INTO Usuario (correo, nombre, apellidos, fecha_ingreso, id_Rol) VALUES
('mod.jose@fundacionloyola.es', 'José', 'Moderador Ejemplo', '2023-01-10 09:00:00', 'M'),
('user.laura@fundacionloyola.es', 'Laura', 'Pérez Gómez', '2023-03-15 08:45:00', 'C');

--CONSULTAS PARA ELIMINAR LAS TABLAS-------------------------------------------------------------------------------------------

DROP TABLE Archivo;
DROP TABLE Hora;
DROP TABLE Solicitud;
DROP TABLE Motivo;
DROP TABLE Curso;
DROP TABLE Usuario;
DROP TABLE Rol;
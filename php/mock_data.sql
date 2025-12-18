USE UO295180_DB;

DELETE FROM observaciones_facilitador;
DELETE FROM results;
DELETE FROM users;

INSERT INTO users (id, profesion, edad, genero, pericia) VALUES
(1, 'Estudiante', 22, 0, 2.1),
(2, 'Diseñador UX', 29, 1, 4.5),
(3, 'Ingeniero de Software', 35, 1, 4.9),
(4, 'Docente', 41, 0, 3.2),
(5, 'Administrador', 50, 1, 3.8),
(6, 'Marketing Digital', 27, 0, 3.5),
(7, 'Contador', 33, 1, 2.8),
(8, 'Médico', 45, 1, 2.4),
(9, 'Abogada', 38, 0, 3.1),
(10, 'Arquitecto', 30, 1, 4.0);

-- ============================
-- INSERTS: results
-- ============================
INSERT INTO results (user_id, dispositivo, tiempo, completado, comentarios, valoracion) VALUES
(1, 'Móvil', 35.4, 1, 'Proceso sencillo, aunque algunas opciones no eran claras.', 7),
(1, 'Ordenador', 28.1, 1, 'Más rápido en PC que en móvil.', 8),

(2, 'Tablet', 22.8, 1, 'Muy intuitivo, diseño agradable.', 9),
(2, 'Móvil', 27.9, 1, 'Texto pequeño en algunas secciones.', 8),

(3, 'Ordenador', 19.6, 1, 'Excelente rendimiento y navegación fluida.', 10),

(4, 'Móvil', 40.3, 0, 'Se perdió en la navegación, faltan indicaciones.', 5),

(5, 'Ordenador', 33.2, 1, 'Correcto, pero podría ser más rápido.', 7),
(5, 'Tablet', 36.7, 1, 'Le costó encontrar el menú secundario.', 6),

(6, 'Móvil', 29.3, 1, 'Interfaz clara, aunque saturada visualmente.', 7),

(7, 'Ordenador', 31.5, 1, 'Algunas funciones no respondían como esperaba.', 6),

(8, 'Tablet', 42.0, 0, 'Muy difícil de usar sin instrucciones.', 4),

(9, 'Móvil', 25.8, 1, 'Cómodo y rápido.', 8),
(9, 'Ordenador', 18.9, 1, 'La mejor experiencia de todas.', 9),

(10, 'Móvil', 30.7, 1, 'Buen desempeño pero animaciones lentas.', 7);

-- ============================
-- INSERTS: observaciones_facilitador
-- ============================
INSERT INTO observaciones_facilitador (id_usuario, comentarios) VALUES
(1, 'Buena disposición; se adaptó fácilmente a los cambios de interfaz.'),
(2, 'Alta pericia, completó las tareas sin dificultad.'),
(3, 'Usuario experto; detectó pequeños detalles de usabilidad.'),
(4, 'Mostró confusión ante menús poco visibles.'),
(5, 'Necesitó pequeñas aclaraciones, pero completó las tareas.'),
(6, 'Algo impaciente, mencionó que la interfaz tiene demasiados elementos.'),
(7, 'Requirió asistencia mínima para tareas avanzadas.'),
(8, 'Se frustró con facilidad ante la falta de instrucciones.'),
(9, 'Observadora y metódica; realizó comentarios valiosos.'),
(10, 'Buen desempeño y rápida adaptación al flujo.');

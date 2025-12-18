<?php
require_once "Cronometro.php"; // Clase Cronometro
require_once "Users.php";
require_once "ObservacionesFacilitador.php";
require_once "Results.php";

session_start();

// Instanciamos el cronómetro (no se muestra al usuario)
if (!isset($_SESSION['cronometro'])) {
    $_SESSION['cronometro'] = new Cronometro();
}

if (!isset($_SESSION['usuario_repo'])) {
    $_SESSION['usuario_repo'] = new Users();
}

if (!isset($_SESSION['results_repo'])) {
    $_SESSION['results_repo'] = new Results();
}

if (!isset($_SESSION['observaciones_repo'])) {
    $_SESSION['observaciones_repo'] = new ObservacionesFacilitador();
}

$mostrarFormularioUsuario = true;
$mostrarFormularioPreguntas = false;
$mostrarFormularioObservador = false;
$completado = true; // Variable que indica si se completaron todas las preguntas

// Manejo del POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Paso 1: Usuario completó datos iniciales
    if (isset($_POST['profesion'], $_POST['edad'], $_POST['genero'], $_POST['pericia']) && isset($_POST['iniciar'])) {
        $profesion = trim($_POST['profesion']);
        $edad = trim($_POST['edad']);
        $genero = isset($_POST['genero']) ? $_POST['genero'] : '';
        $pericia = trim($_POST['pericia']);


        // Guardar los datos del usuario en sesión para usarlos más adelante
        $_SESSION['usuario'] = [
            'profesion' => $profesion,
            'edad' => $edad,
            'genero' => $genero,
            'pericia' => $pericia
        ];
        // Arrancamos el cronómetro
        $_SESSION['cronometro']->arrancar();
        $mostrarFormularioUsuario = false;
        $mostrarFormularioPreguntas = true;
        $mostrarFormularioObservador = false;

    }

    // Paso 2: Usuario completó la prueba de 10 preguntas
    if (isset($_POST['finalizar'])) {
        $respuestas = [];
        $resultados = '';
        for ($i = 1; $i <= 10; $i++) {
            $respuestas[$i] = trim($_POST["pregunta$i"] ?? '');
            if ($respuestas[$i] === '*')
                $completado = false;
            $resultados .= $respuestas[$i];
            if ($i < 10) {
                $resultados .= "@";
            }
        }

        $comentarios = trim($_POST['comentarios'] ?? '');
        $valoracion = trim($_POST['valoracion'] ?? '');

        // Detectar dispositivo (user agent)
        $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        $_SESSION['cronometro']->parar();
        $tiempo = $_SESSION['cronometro']->getTiempo();
        // Guardar todos los datos en sesión para que tú manejes la base de datos
        $_SESSION['prueba'] = [
            'respuestas' => $respuestas,
            'comentarios' => $comentarios,
            'valoracion' => $valoracion,
            'dispositivo' => $dispositivo,
            'tiempo' => $tiempo,
            'completado' => $completado,
            'resultados' => $resultados
        ];

        $mostrarFormularioPreguntas = false;
        $mostrarFormularioUsuario = false;
        $mostrarFormularioObservador = true;
    }

    if (isset($_POST['observaciones_facil'])) {
        $observaciones = trim($_POST['observaciones'] ?? '');
        $dispositivo = trim($_POST['dispositivo'] ?? '');


        $_SESSION['user_id'] = $_SESSION['usuario_repo']->create(
            $_SESSION['usuario']['profesion'],
            $_SESSION['usuario']['edad'],
            $_SESSION['usuario']['genero'],
            $_SESSION['usuario']['pericia']
        );

        $_SESSION['result_id'] = $_SESSION['results_repo']->create(
            $_SESSION['user_id'],
            $dispositivo,
            $_SESSION['prueba']['tiempo'],
            $_SESSION['prueba']['completado'],
            $_SESSION['prueba']['comentarios'],
            $_SESSION['prueba']['valoracion'],
            $_SESSION['prueba']['resultados']
        );

        $_SESSION['observacion_id'] = $_SESSION['observaciones_repo']->create(
            $_SESSION['user_id'],
            $observaciones
        );

        $mostrarFormularioUsuario = true;
        $mostrarFormularioPreguntas = false;
        $mostrarFormularioObservador = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Prueba de Usabilidad MotoGP</title>
    <link rel="stylesheet" href="../estilo/estilo.css">
    <link rel="stylesheet" href="../estilo/layout.css">
</head>

<body>
    <h1>Prueba de Usabilidad MotoGP</h1>

    <?php if ($mostrarFormularioUsuario): ?>
        <h2>Datos del usuario</h2>
        <form method="POST">
            <label for="profesion">Profesión: </label>
            <input type="text" id="profesion" name="profesion" value="<?= $_POST['profesion'] ?? '' ?>" required>

            <label for="edad">Edad: </label>
            <input type="number" id="edad" name="edad" value="<?= $_POST['edad'] ?? '' ?>" required>

            <label for="genero">Género: </label>
            <select name="genero" id="genero" required>
                <option value="">Selecciona...</option>
                <option value="1" <?= (($_POST['genero'] ?? '') == '1') ? 'selected' : '' ?>>Masculino</option>
                <option value="0" <?= (($_POST['genero'] ?? '') == '0') ? 'selected' : '' ?>>Femenino</option>
            </select>

            <label for="pericia">Pericia: </label>
            <input type="number" id="pericia" step="0.1" name="pericia" value="<?= $_POST['pericia'] ?? '' ?>" required>

            <button type="submit" name="iniciar">Iniciar prueba</button>
        </form>
    <?php endif; ?>

    <?php if ($mostrarFormularioPreguntas): ?>
        <h2>Preguntas de la prueba</h2>
        <p>Si no se contesta a alguna, rellenar con *</p>
        <form method="POST">
            <label for="pregunta1">Cual es el piloto del que se habla en el apartado Piloto?</label>
            <input type="text" id="pregunta1" name="pregunta1" required>

            <label for="pregunta2">Que circuito aparece en el apartado Circuito?</label>
            <input type="text" id="pregunta2" name="pregunta2" required>

            <label for="pregunta3">Quien es el piloto ganador en el apartado Clasificaciones?</label>
            <input type="text" id="pregunta3" name="pregunta3" required>

            <label for="pregunta4">En el apartado Inicio, de que circuito son las imágenes?</label>
            <input type="text" id="pregunta4" name="pregunta4" required>

            <label for="pregunta5">Cual es el primer juego que aparece en el apartado Juegos?</label>
            <input type="text" id="pregunta5" name="pregunta5" required>

            <label for="pregunta6">En el apartado Meteorología, de qué ciudad se aportan los datos meteorológicos?</label>
            <input type="text" id="pregunta6" name="pregunta6" required>
            
            <label for="pregunta7">Caunto pesa el piloto en el apartado Piloto?</label>
            <input type="text" id="pregunta7" name="pregunta7" required>

            <label for="pregunta8">Cuál es la marca de la moto del piloto?</label>
            <input type="text" id="pregunta8" name="pregunta8" required>

            <label for="pregunta9">Cauntos puntos consiguió el piloto en la temporada 2024?</label>
            <input type="text" id="pregunta9" name="pregunta9" required>

            <label for="pregunta10">En que año nació el piloto?</label>
            <input type="text" id="pregunta10" name="pregunta10" required>

            <label for="comentarios">Comentarios:</label><br>
            <textarea name="comentarios" id="comentarios" rows="4" cols="50" required></textarea>

            <label for="valoracion">Valoración (1-10):</label>
            <input type="number" id="valoracion" name="valoracion" min="1" max="10" required>

            <button type="submit" name="finalizar">Finalizar prueba</button>
        </form>
    <?php endif; ?>

    <?php if ($mostrarFormularioObservador): ?>
        <h2>Formulario Observador</h2>
        <form method="POST">
            <label for="dispositivo">Dispositivo: </label>
            <select name="dispositivo" id="dispositivo" required>
                <option value="">Selecciona...</option>
                <option value="ordenador">Ordenador</option>
                <option value="tablet">Tablet</option>
                <option value="movil">Móvil</option>
            </select>
            <label for="observaciones">Observaciones del facilitador:</label><br>
            <textarea name="observaciones" id="observaciones" rows="6" cols="50" required></textarea><br><br>
            <button type="submit" name="observaciones_facil">Guardar Observaciones</button>
        </form>
    <?php endif; ?>

</body>

</html>
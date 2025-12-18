<?php
require_once "php/Cronometro.php";
session_start();


if (isset($_SESSION["cronometro"])) {
    $cronometro = $_SESSION["cronometro"];
} else {
    $cronometro = new Cronometro();
}
$mensaje = "";

if (count($_POST)>0) {
    if(isset($_POST['arrancar'])) {
        $cronometro->arrancar();
        $mensaje = "Cronómetro arrancado.";
    } elseif (isset($_POST['parar'])) {
        $cronometro->parar();
        $mensaje = "Cronómetro parado.";
    } elseif (isset($_POST['mostrar'])) {
        $tiempo = $cronometro->mostrar();
        $mensaje = "Tiempo transcurrido: " . $tiempo;
    }
}
$_SESSION["cronometro"] = $cronometro;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name="author" content="Mario Orviz Viesca" />
    <meta name="description" content="Página de cronometro en PHP para la aplicación MotoGP Desktop" />
    <meta name="keywords" content="MotoGP, carreras, motos, cronometro" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link type="text/css" rel="stylesheet" href="estilo/estilo.css" />
    <link type="text/css" rel="stylesheet" href="estilo/layout.css" />
    <title>MotoGP - Cronometro PHP</title>
    <link rel="icon" type="image/ico" href="multimedia/imagenes/moto-1.ico" sizes="48x48">
</head>

<body>
    <header>
        <h1><a href="index.html" title="inicio">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html" title="Inicio">Inicio</a>
            <a href="piloto.html" title="Piloto">Piloto</a>
            <a href="clasificaciones.php" title="Clasificaciones">Clasificaciones</a>
            <a href="meteorologia.html" title="Meteorologia">Meteorología</a>
            <a href="circuito.html" title="Circuito">Circuito</a>
            <a href="juegos.html" title="Juegos">Juegos</a>
            <a href="ayuda.html" title="Ayuda">Ayuda</a>
            <a href="cronometro.php" title="Cronometro PHP"  class="active">Cronómetro PHP</a>
        </nav>
    </header>

    <p><a href="index.html">Inicio</a> > <a href="juegos.html">Juegos</a> > Cronómetro PHP</p>

    <h2>Prueba del Cronómetro</h2>

    <form method="post">
        <button type="submit" name="arrancar" value="arrancar">Arrancar</button>
        <button type="submit" name="parar" value="parar">Parar</button>
        <button type="submit" name="mostrar" value="mostrar">Mostrar</button>
    </form>

    <?php if ($mensaje != ""): ?>
        <p><?= $mensaje ?></p>
    <?php endif; ?>

</body>

</html>
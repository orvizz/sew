<?php
    require_once 'php/DBManagerUsabilidad.php';
    $db = new DBManager();
    $db->connect();
    echo "Conexión a la base de datos 'usabilidad' establecida correctamente.";
    $db->close();
?>
<?php
class Configuracion
{
    private $host = "localhost";
    private $user = "DBUSER2025";
    private $pass = "DBPSWD2025";
    private $dbname = "UO295180_DB";
    private $port = 3306;


    private function conectarBD($usarDB = true)
    {
        $db = $usarDB ? $this->dbname : null;
        $conn = new mysqli($this->host, $this->user, $this->pass, $db, $this->port);

        if ($conn->connect_error) {
            return null;
        }
        return $conn;
    }

    public function reiniciarBaseDeDatos()
    {
        $conn = $this->conectarBD();
        if(!$conn) return "La base de datos no existe";

        $query = "
            DELETE FROM observaciones_facilitador;
            DELETE FROM results;
            DELETE FROM users;
        ";

        if ($conn->multi_query($query)) {
            while ($conn->more_results() && $conn->next_result()) {
                $result = $conn->store_result();
                if ($result)
                    $result->free();
            }
        } else {
            return "Error al reiniciar: " . $conn->error;
        }

        $conn->close();
        return "Tablas vaciadas correctamente";
    }

    public function eliminarBaseDeDatos()
    {
        $conn = $this->conectarBD(false);
        if (!$conn) return "No se pudo conectar al servidor MySQL.";

        $query = "DROP DATABASE IF EXISTS " . $this->dbname;

        if (!$conn->query($query)) {
            return "Error al eliminar BD: " . $conn->error;
        }

        $conn->close();
        return "Base de datos eliminada";
    }

    public function exportarCSV()
    {
        $conn = $this->conectarBD();
        if(!$conn) return "La base de datos no existe";

        $conn->set_charset('utf8mb4');

        $exportDir = __DIR__ . DIRECTORY_SEPARATOR . "export";

        if (!is_dir($exportDir)) {
            if (!mkdir($exportDir, 0777, true)) {
                $conn->close();
                return "No se pudo crear el directorio para exportar los datos";
            }
        }

        $tablas = ["users", "results", "observaciones_facilitador"];
        $resultados = "";

        foreach ($tablas as $tabla) {
            $safeTabla = $conn->real_escape_string($tabla);
            $query = "SELECT * FROM `$safeTabla`";

            $result = $conn->query($query);
            if (!$result) {
                $resultados .= "No se pudo exportar `$tabla`: " . $conn->error;
                continue;
            }

            $filePath = $exportDir . DIRECTORY_SEPARATOR . $tabla . '.csv';
            $fp = @fopen($filePath, 'w');
            if (!$fp) {
                $resultados .= "No se pudo escribir el archivo $filePath";
                $result->free();
                continue;
            }

            fwrite($fp, "\xEF\xBB\xBF");

            // Cabeceras
            $fields = $result->fetch_fields();
            $headers = array_map(function ($f) {
                return $f->name; }, $fields);
            fputcsv($fp, $headers);

            // Filas
            $rowsWritten = 0;
            while ($row = $result->fetch_assoc()) {
                
                fputcsv($fp, $row);
                $rowsWritten++;
            }

            fclose($fp);
            $result->free();
        }

        $conn->close(); 
        if($resultados == "") return "Datos exportados con éxito";
        return $resultados;
    }


    public function crearBDDesdeSQL()
    {
        $archivoSQL = "database.sql";
        if (!file_exists($archivoSQL)) {
            return "No existe el archivo SQL";
        }

        $conn = $this->conectarBD(false);
        $sql = file_get_contents($archivoSQL);

        if ($conn->multi_query($sql)) {
            while ($conn->more_results() && $conn->next_result()) {
                $result = $conn->store_result();
                if ($result)
                    $result->free();
            }
        } else {
            return  "Error al crear BD: " . $conn->error;
        }

        $conn->close();

        return "Base de datos creada correctamente";
    }
}
?>
<?php
$configuracion;

if (isset($_SESSION["configuracion"])) {
    $configuracion = $_SESSION["configuracion"];
} else {
    $configuracion = new Configuracion();
}

$mensaje = "";
if (count($_POST) > 0) {
    if (isset($_POST['borrarTablas'])) {
        $mensaje = $configuracion->reiniciarBaseDeDatos();
        
    } elseif (isset($_POST['eliminarBD'])) {
        $mensaje = $configuracion->eliminarBaseDeDatos();
        
    } elseif (isset($_POST['exportCSV'])) {
        $mensaje = $configuracion->exportarCSV();
        
    } elseif (isset($_POST['crearBD'])) {
        $mensaje = $configuracion->crearBDDesdeSQL();
        
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name="author" content="Mario Orviz Viesca" />
    <meta name="description" content="Página de usabilidad en PHP para la aplicación MotoGP Desktop" />
    <meta name="keywords" content="MotoGP, carreras, motos, cronometro" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link type="text/css" rel="stylesheet" href="../estilo/estilo.css" />
    <link type="text/css" rel="stylesheet" href="../estilo/layout.css" />
    <title>MotoGP - Usabilidad</title>
    <link rel="icon" type="image/ico" href="../multimedia/imagenes/moto-1.ico" sizes="48x48">
</head>

<body>
    <h2>Configuracion</h2>

    <form method="post">
        <button type="submit" name="borrarTablas" value="borrarTablas">Borrar tablas</button>
        <button type="submit" name="eliminarBD" value="eliminarBD">Eliminar base de datos</button>
        <button type="submit" name="exportCSV" value="exportCSV">Exportar datos a CSV</button>
        <button type="submit" name="crearBD" value="crearBD">Crear base de datos</button>
    </form>

    <?php if ($mensaje != ""): ?>
        <p><?= $mensaje ?></p>
    <?php endif; ?>

</body>

</html>
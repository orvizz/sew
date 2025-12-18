<?php

class ObservacionesFacilitador {

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

    /* CREATE */
    public function create($id_usuario, $comentarios) {
        $conn = $this->conectarBD();
        $stmt = $conn->prepare("
            INSERT INTO observaciones_facilitador (id_usuario, comentarios)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $id_usuario, $comentarios);
        $stmt->execute();

        $nuevoId = $conn->insert_id;

        $stmt->close();
        $conn->close();

        return $nuevoId;
    }

}

?>

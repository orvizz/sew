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

    ///* READ */
    //public function getById($id) {
    //    $stmt = $this->conn->prepare("SELECT * FROM observaciones_facilitador WHERE id_observacion = ?");
    //    $stmt->bind_param("i", $id);
    //    $stmt->execute();
    //    return $stmt->get_result()->fetch_assoc();
    //}
//
    //public function getAll() {
    //    $result = $this->conn->query("SELECT * FROM observaciones_facilitador");
    //    return $result->fetch_all(MYSQLI_ASSOC);
    //}
//
    //public function getByUser($id_usuario) {
    //    $stmt = $this->conn->prepare("SELECT * FROM observaciones_facilitador WHERE id_usuario = ?");
    //    $stmt->bind_param("i", $id_usuario);
    //    $stmt->execute();
    //    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    //}
//
    ///* UPDATE */
    //public function update($id, $comentarios) {
    //    $stmt = $this->conn->prepare("
    //        UPDATE observaciones_facilitador
    //        SET comentarios=?
    //        WHERE id_observacion=?
    //    ");
    //    $stmt->bind_param("si", $comentarios, $id);
    //    return $stmt->execute();
    //}
//
    ///* DELETE */
    //public function delete($id) {
    //    $stmt = $this->conn->prepare("DELETE FROM observaciones_facilitador WHERE id_observacion = ?");
    //    $stmt->bind_param("i", $id);
    //    return $stmt->execute();
    //}
}

?>

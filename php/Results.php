<?php

class Results {

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
    public function create($user_id, $dispositivo, $tiempo, $completado, $comentarios, $valoracion, $resultados) {
        $conn = $this->conectarBD();
        $stmt = $conn->prepare("
            INSERT INTO results (user_id, dispositivo, tiempo, completado, comentarios, valoracion, resultados)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isdisis", $user_id, $dispositivo, $tiempo, $completado, $comentarios, $valoracion, $resultados);
        $stmt->execute();

        $nuevoId = $conn->insert_id;

        $stmt->close();
        $conn->close();

        return $nuevoId;
    }

    ///* READ */
    //public function getById($id) {
    //    $stmt = $this->conn->prepare("SELECT * FROM results WHERE resultado_id = ?");
    //    $stmt->bind_param("i", $id);
    //    $stmt->execute();
    //    return $stmt->get_result()->fetch_assoc();
    //}
//
    //public function getAll() {
    //    $result = $this->conn->query("SELECT * FROM results");
    //    return $result->fetch_all(MYSQLI_ASSOC);
    //}
//
    ///* READ por usuario */
    //public function getByUser($user_id) {
    //    $stmt = $this->conn->prepare("SELECT * FROM results WHERE user_id = ?");
    //    $stmt->bind_param("i", $user_id);
    //    $stmt->execute();
    //    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    //}
//
    ///* UPDATE */
    //public function update($id, $user_id, $dispositivo, $tiempo, $completado, $comentarios, $valoracion) {
    //    $stmt = $this->conn->prepare("
    //        UPDATE results
    //        SET user_id=?, dispositivo=?, tiempo=?, completado=?, comentarios=?, valoracion=?
    //        WHERE resultado_id=?
    //    ");
    //    $stmt->bind_param("isdisii", $user_id, $dispositivo, $tiempo, $completado, $comentarios, $valoracion, $id);
    //    return $stmt->execute();
    //}
//
    ///* DELETE */
    //public function delete($id) {
    //    $stmt = $this->conn->prepare("DELETE FROM results WHERE resultado_id = ?");
    //    $stmt->bind_param("i", $id);
    //    return $stmt->execute();
    //}
}

?>

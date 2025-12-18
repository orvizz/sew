<?php

class Users {

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
    /*
    /* CREATE */
    public function create($profesion, $edad, $genero, $pericia) {
        $conn = $this->conectarBD();
        $stmt = $conn->prepare("
            INSERT INTO users (profesion, edad, genero, pericia)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sidi", $profesion, $edad, $genero, $pericia);
        $stmt->execute();

        $nuevoId = $conn->insert_id;

        $stmt->close();
        $conn->close();

        return $nuevoId;
    }

    ///* READ (uno por id) */
    //public function getById($id) {
    //    $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
    //    $stmt->bind_param("i", $id);
    //    $stmt->execute();
    //    return $stmt->get_result()->fetch_assoc();
    //}
//
    ///* READ (todos) */
    //public function getAll() {
    //    $result = $this->conn->query("SELECT * FROM users");
    //    return $result->fetch_all(MYSQLI_ASSOC);
    //}
//
    ///* UPDATE */
    //public function update($id, $profesion, $edad, $genero, $pericia) {
    //    $stmt = $this->conn->prepare("
    //        UPDATE users
    //        SET profesion=?, edad=?, genero=?, pericia=?
    //        WHERE id=?
    //    ");
    //    $stmt->bind_param("sidii", $profesion, $edad, $genero, $pericia, $id);
    //    return $stmt->execute();
    //}
//
    ///* DELETE */
    //public function delete($id) {
    //    $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
    //    $stmt->bind_param("i", $id);
    //    return $stmt->execute();
    //}
    //
}

?>

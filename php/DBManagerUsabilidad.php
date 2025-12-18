<?php

class DBManager
{

    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;
    public $conn;

    /**
     * Constructor: inicializa parámetros
     * $port es opcional, por defecto 3306
     */
    public function __construct()
    {
        $this->host = "localhost";
        $this->user = "DBUSER2025";
        $this->pass = "DBPSWD2025";
        $this->dbname = "UO295180_DB";
        $this->port = "3306";

        $this->connect();
        $this->initializeDatabase();
    }

    /**
     * Conectar a MySQL
     */
    public function connect()
    {
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            null,
            $this->port
        );

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    private function initializeDatabase()
    {
        $sql = file('database.sql');
        if ($this->conn->multi_query(implode("", $sql))) {
            do {
                if ($result = $this->conn->store_result()) {
                    $result->free();
                }
            } while ($this->conn->more_results() && $this->conn->next_result());
        }
        $this->conn->select_db($this->dbname);
    }

    public function executeQuery($query)
    {
        try {
            if (!$this->conn) {
                $this->connect();
            }
            if (!$this->conn->multi_query($query)) {
                throw new Exception("Error ejecutando query: " . $this->conn->error);
            }

            // Limpiar todos los resultados que genera multi_query
            while ($this->conn->more_results() && $this->conn->next_result()) {
                $result = $this->conn->store_result();
                if ($result) {
                    $result->free();
                }
            }
        } catch (Exception) {

            $logDir = __DIR__ . '/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            $mysqliErr = isset($this->conn) ? $this->conn->error : null;
            $last = error_get_last();
            $entry = sprintf(
                "[%s] executeQuery exception. mysqli_error=%s; php_last_error=%s\n",
                date('c'),
                $mysqliErr ?: 'n/a',
                $last ? json_encode($last) : 'n/a'
            );
            error_log($entry, 3, $logDir . '/db_errors.log');
            error_log($entry);
        }
    }
    /**
     * Cierra conexión
     */
    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>
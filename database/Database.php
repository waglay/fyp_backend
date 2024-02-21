<?php
// $host = "localhost";      // insert localhost
// $dbName = "fyp";    // insert database name
// $user = "root";
// $pass = "";

// // mysqli_connect($host, $user, $pass, $dbName) or die("Database connection failed!");
// $conn = new mysqli($host, $user, $pass, $dbName);
// if ($conn->connect_error) {
//     die("Database connection failed!" . $conn->connect_error);
// } else {
//     // echo "Database connection successful!";
// }
// ###############


class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = "localhost";
        $dbName = "fyp";
        $user = "root";
        $pass = "";

        $this->conn = new mysqli($host, $user, $pass, $dbName);
        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }

        // Set the character set to UTF-8
        $this->conn->set_charset("utf8");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
    // Destructor method to close the connection
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    // Use $conn for database operations
} catch (Exception $e) {
    // Handle the exception
    echo "Database connection failed: " . $e->getMessage();
}

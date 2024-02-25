
# Backend for fyp

generate a api endpoint that take data in inserts the user deatil in db using
```php
<?php
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
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    // Use $conn for database operations
} catch (Exception $e) {
    // Handle the exception
    echo "Database connection failed: " . $e->getMessage();
}
```
based upon this db table
```
CREATE TABLE member (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(255) NOT NULL,
    member_email VARCHAR(255) NOT NULL,
    member_PASSWORD VARCHAR(255) NOT NULL,
    member_phone VARCHAR(20),
    member_ADDRESS VARCHAR(255),
    member_hight DECIMAL(5,2),
    member_weight DECIMAL(5,2)
);
```
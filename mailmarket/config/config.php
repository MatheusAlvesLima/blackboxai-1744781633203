<?php
// config.php - Global configuration variables and database connection

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mailmarket');
define('DB_USER', 'root');
define('DB_PASS', '');

// Other global config variables
define('BASE_URL', '/mailmarket/public/');

// PDO database connection function
function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            // Check if pdo_mysql driver is available
            if (!in_array('mysql', PDO::getAvailableDrivers())) {
                throw new Exception('PDO MySQL driver is not enabled.');
            }
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        } catch (Exception $e) {
            die('Configuration error: ' . $e->getMessage());
        }
    }
    return $pdo;
}
?>

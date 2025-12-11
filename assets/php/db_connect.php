<?php
/**
 * Database Connection Handler
 * 
 * This file contains the database connection logic using PDO for PostgreSQL.
 * It provides a getConnection() function that can be used throughout the application.
 */

class Database {
    private static $conn = null;

    /**
     * Get database connection
     * 
     * @return PDO Returns a PDO database connection object
     * @throws PDOException If connection fails
     */
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                // Database connection parameters
                $host = 'localhost';
                $port = '5432';
                $dbname = 'pbl_db';
                $user = 'postgres';
                $password = '12345';

                // Create DSN (Data Source Name)
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                
                // Set PDO options
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                // Create PDO instance
                self::$conn = new PDO($dsn, $user, $password, $options);
                
            } catch (PDOException $e) {
                // Log the error (in a real application, you might want to log this to a file)
                error_log("Database Connection Error: " . $e->getMessage());
                
                // Re-throw the exception to be handled by the calling code
                throw new PDOException("Connection failed: " . $e->getMessage(), (int)$e->getCode());
            }
        }
        
        return self::$conn;
    }

    /**
     * Prevent creating multiple instances
     */
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize database connection");
    }
}

// Function to get database connection (for backward compatibility)
function getConnection() {
    return Database::getConnection();
}

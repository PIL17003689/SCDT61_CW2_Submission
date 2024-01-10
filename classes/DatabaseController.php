<?php

// Extends the PDO class to create a custom DatabaseController
class DatabaseController extends PDO {

    // Constructor for DatabaseController
    public function __construct(string $dsn, string $username, string $password, array $options = [])
    {
        // Default options for PDO connection
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Error mode to exceptions for better error handling
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
            PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation of prepared statements
        ];
 
        // Construct the parent class (PDO) with the DSN, username, password, and options
        parent::__construct($dsn, $username, $password, $options);
    }

    // Method to execute SQL queries
    public function runSQL(string $sql, array $args = null)
    {
        // If there are no arguments, execute the query directly
        if (!$args)
        {
            return $this->query($sql);
        }

        // Prepare and execute the SQL statement with arguments
        $statement = $this->prepare($sql);
        $statement->execute($args);
        return $statement; // Return the statement object
    }

}
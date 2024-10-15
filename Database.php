<?php

class Database {
    private $username = 'docker';
    private $password = 'docker';
    private $host = 'db';
    private $database = 'db';
    private $port = 5432;




    public function connect()
    {
        try {
            $conn = new PDO(
                "pgsql:host=$this->host;
                port=$this->port;
                dbname=$this->database", $this->username, $this->password,
                ["sslmode"  => "prefer"]
            );
            
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }  
}
<?php

require_once __DIR__ . '/../../Database.php';

class User {
    private $conn;
    private $id;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function login($email, $password)
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                return true;
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function register($name, $email, $password, $confirmPassword)
    {
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            return "All fields are required!";
        }
    
        if ($password !== $confirmPassword) {
            return "The passwords do not match!";
        }
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        try {
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            return true;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23505) { 
                return "The email address provided is already registered.";
            }

            return "An error occurred while registering. Please try again later.";
        }
    }

    public function getNameById($userId)
    {
        try {
            $sql = "SELECT name FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user['name'];

        } catch (PDOException $e) {
            return null;
        }
    }
}

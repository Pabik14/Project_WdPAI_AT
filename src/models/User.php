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
            return "Wszystkie pola są wymagane!";
        }
    
        if ($password !== $confirmPassword) {
            return "Hasła się nie zgadzają!";
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
            // Sprawdź typ błędu (np. unikalność emaila)
            if ($e->getCode() == 23505) { // Kod błędu dla naruszenia unikalności
                return "Podany email jest już zarejestrowany.";
            }

            // Inne błędy
            return "Wystąpił błąd podczas rejestracji. Spróbuj ponownie później.";
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

<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . "/../../Database.php";

class DashboardController extends AppController {
    private $conn;

    public function __construct()
    {
        // Inicjalizuj połączenie z bazą danych
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function dashboard()
    {
        session_start();

        // Sprawdź, czy sesja użytkownika jest aktywna
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $userEmail = $_SESSION['email'];
        if ($userEmail == 'admin@admin.pl'){
            header('Location: /adminPanel');
            exit();
        }
        // Pobierz user_id z sesji
        $userId = $_SESSION['user_id'];

        // Stwórz obiekt User i pobierz nazwę użytkownika
        $user = new User();
        $userName = $user->getNameById($userId);

        // Renderuje widok dashboard i przekazuje nazwę użytkownika do widoku
        $this->render('dashboard', ['userName' => $userName]);
    }

    // Metoda do pobierania statystyk anime z bazy danych
    public function getAnimeStats()
    {
        session_start();

        // Sprawdź, czy sesja użytkownika jest aktywna
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $userEmail = $_SESSION['email'];
        if ($userEmail == 'admin@admin.pl'){
            header('Location: /adminPanel');
            exit();
        }
        $userId = $_SESSION['user_id'];

        try {
            // Pobierz statystyki statusu anime
            $statusSql = "SELECT status, COUNT(*) as count FROM anime_list WHERE user_id = :user_id GROUP BY status";
            $stmt = $this->conn->prepare($statusSql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pobierz statystyki typu anime
            $typeSql = "SELECT type, COUNT(*) as count FROM anime_list WHERE user_id = :user_id GROUP BY type";
            $stmt = $this->conn->prepare($typeSql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $typeData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Zwrot danych w formacie JSON
            header('Content-Type: application/json');
            echo json_encode(['statusData' => $statusData, 'typeData' => $typeData]);
            exit();
        } catch (Exception $e) {
            // Obsługa błędu w przypadku problemów z bazą danych
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Błąd podczas pobierania statystyk: ' . $e->getMessage()]);
            exit();
        }
    }
}

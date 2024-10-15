<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . "/../../Database.php";

class AnimeController extends AppController {
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Wyświetla listę anime zalogowanego użytkownika
    public function showList()
    {
        session_start();

        // Sprawdź, czy użytkownik jest zalogowany
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // Jeśli użytkownik nie jest zalogowany, przekieruj na stronę logowania
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

        // Pobieranie danych z bazy danych
        $sql = "SELECT id, anime_name, category, type, status, episodes_count 
        FROM anime_list 
        WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Pobierz wszystkie wyniki i upewnij się, że `null` jest zastępowane domyślną wartością
        $animeList = array_map(function($anime) {
        return [
        'id' => $anime['id'] ?? '',
        'anime_name' => $anime['anime_name'] ?? '',
        'category' => $anime['category'] ?? '',
        'type' => $anime['type'] ?? '',
        'status' => $anime['status'] ?? '',
        'episodes_count' => $anime['episodes_count'] ?? 0
        ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));


        // Renderuj widok `animelist.html` z przekazanymi wynikami
        $this->render('animelist', [
            'userName' => $userName,
            'animeList' => $animeList
        ]);
    }

 
    public function searchAnime()
    {
        session_start();
    
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Nieautoryzowany dostęp.']);
            exit();
        }
        $userEmail = $_SESSION['email'];
        if ($userEmail == 'admin@admin.pl'){
            header('Location: /adminPanel');
            exit();
        }
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
    
            $userId = $_SESSION['user_id'];
            
            try {
                $query = isset($decoded['search']) && !empty($decoded['search']) ? '%' . trim($decoded['search']) . '%' : '%';
                $status = isset($decoded['status']) && !empty($decoded['status']) ? $decoded['status'] : '%';
    
                $sql = "SELECT id, anime_name, category, type, status, episodes_count 
                        FROM anime_list 
                        WHERE user_id = :user_id 
                        AND anime_name ILIKE :query 
                        AND status ILIKE :status";
    
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':query', $query, PDO::PARAM_STR);
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    
                $stmt->execute();
                $animeList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                echo json_encode($animeList);
                exit();
            } catch (Exception $e) {
                echo json_encode(['error' => 'Błąd podczas przetwarzania zapytania: ' . $e->getMessage()]);
                exit();
            }
        } else {
            echo json_encode(['error' => 'Nieprawidłowy typ treści.']);
            exit();
        }
    }
    
    public function deleteAnime()
    {
        session_start();
    
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Nieautoryzowany dostęp.']);
            exit();
        }
    
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
    
            if (isset($decoded['anime_id'])) {
                try {
                    $animeId = $decoded['anime_id'];
                    $userId = $_SESSION['user_id'];
    
                    $sql = "DELETE FROM anime_list WHERE id = :anime_id AND user_id = :user_id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => 'Anime zostało usunięte.']);
                    } else {
                        echo json_encode(['error' => 'Nie udało się usunąć anime.']);
                    }
                    exit();
                } catch (Exception $e) {
                    echo json_encode(['error' => 'Błąd podczas przetwarzania zapytania: ' . $e->getMessage()]);
                    exit();
                }
            } else {
                echo json_encode(['error' => 'Brak id anime do usunięcia.']);
                exit();
            }
        } else {
            echo json_encode(['error' => 'Nieprawidłowy typ treści.']);
            exit();
        }
    }
    

}

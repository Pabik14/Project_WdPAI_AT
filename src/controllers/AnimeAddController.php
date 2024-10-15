<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . "/../../Database.php";

class AnimeAddController extends AppController {
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Wyświetl formularz dodawania anime
    public function showAddAnimeForm()
    {
        // session_start();

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

        // Renderuj widok `addAnime.html`
        $this->render('addAnime', ['userName' => $userName]);
    }

    // Dodaj anime do bazy danych
    public function addAnime()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        $userEmail = $_SESSION['email'];
        if ($userEmail == 'admin@admin.pl'){
            header('Location: /adminPanel');
            exit();
        }
        // Sprawdź, czy formularz został wysłany metodą POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Pobierz dane z formularza
            $userId = $_SESSION['user_id'];
            $animeName = $_POST['anime_name'];
            $category = $_POST['category'];
            $type = $_POST['type'];
            $status = $_POST['status'];
            $episodesCount = intval($_POST['episodes_count']);

            // Walidacja danych
            if ($episodesCount < 1) {
                echo "Liczba odcinków musi być większa niż 0!";
                return;
            }

            // Dodaj anime do bazy danych
            $sql = "INSERT INTO anime_list (user_id, anime_name, category, type, status, episodes_count) 
                    VALUES (:user_id, :anime_name, :category, :type, :status, :episodes_count)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':anime_name', $animeName, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':episodes_count', $episodesCount, PDO::PARAM_INT);

            // Wykonaj zapytanie
            try {
                $stmt->execute();
                // Przekieruj na listę anime po dodaniu
                header('Location: /animelist');
                exit();
            } catch (PDOException $e) {
                echo "Błąd podczas dodawania anime: " . $e->getMessage();
            }
        } else {
            // Jeśli metoda żądania nie jest POST, przekieruj na formularz dodawania
            $this->showAddAnimeForm();
        }
    }
}

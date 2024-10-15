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

    public function showAddAnimeForm()
    {
        
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

        $user = new User();
        $userName = $user->getNameById($userId);

        $this->render('addAnime', ['userName' => $userName]);
    }

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $animeName = $_POST['anime_name'];
            $category = $_POST['category'];
            $type = $_POST['type'];
            $status = $_POST['status'];
            $episodesCount = intval($_POST['episodes_count']);

            if ($episodesCount < 1) {
                echo "Liczba odcinków musi być większa niż 0!";
                return;
            }

            $sql = "INSERT INTO anime_list (user_id, anime_name, category, type, status, episodes_count) 
                    VALUES (:user_id, :anime_name, :category, :type, :status, :episodes_count)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':anime_name', $animeName, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':episodes_count', $episodesCount, PDO::PARAM_INT);

            try {
                $stmt->execute();
                header('Location: /animelist');
                exit();
            } catch (PDOException $e) {
                echo "Error while adding anime: " . $e->getMessage();
            }
        } else {
            $this->showAddAnimeForm();
        }
    }
}

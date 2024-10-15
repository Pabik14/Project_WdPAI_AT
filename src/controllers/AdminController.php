<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . "/../../Database.php";

class AdminController extends AppController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

  public function adminPanel() {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }

    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];

    if ($userEmail !== 'admin@admin.pl') {
        $user = new User();
        $userName = $user->getNameById($userId);
        $this->render('dashboard', ['userName' => $userName]);
        exit();
    }

    $stmt = $this->conn->prepare("SELECT id, name, email FROM users WHERE email != 'admin@admin.pl'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->render('adminPanel', ['users' => $users]);
}

    public function deleteUser() {
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@admin.pl') {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['user_id'])) {
            $userId = $_POST['user_id'];

            try {
                $stmt = $this->conn->prepare("DELETE FROM anime_list WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                header('Location: /adminPanel'); 
                exit();
            } catch (PDOException $e) {
                echo "Error when deleting user: " . $e->getMessage();
            }
        }
    }

public function downloadDatabase() {
    session_start();

    if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@admin.pl') {
        header('Location: /login');
        exit();
    }
    $databaseDir = '/app/database';
    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0777, true);
    }

    $dumpsDir = $databaseDir . '/dumps';
    if (!is_dir($dumpsDir)) {
        mkdir($dumpsDir, 0777, true);
    }

    $backupFile = $dumpsDir . '/backup_' . date('Ymd_His') . '.sql';

    $command = "PGPASSWORD='docker' pg_dump -h db -U docker -d db -F c -b -v -f $backupFile 2>&1";

    exec($command, $output, $resultCode);

    if ($resultCode === 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backupFile));

        readfile($backupFile);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while executing a database dump.',
            'details' => $output 
        ]);
    }
}

    
    
}

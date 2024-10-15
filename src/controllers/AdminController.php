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

    // Sprawdź, czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }

    // Pobierz email i ID użytkownika z sesji
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];

    // Sprawdź, czy to admin
    if ($userEmail !== 'admin@admin.pl') {
        $user = new User();
        $userName = $user->getNameById($userId);
        // Jeśli to nie admin, przekieruj na dashboard
        $this->render('dashboard', ['userName' => $userName]);
        exit();
    }

    // Pobierz wszystkich użytkowników z bazy, z wyjątkiem admina
    $stmt = $this->conn->prepare("SELECT id, name, email FROM users WHERE email != 'admin@admin.pl'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Renderuj widok adminPanel z listą użytkowników
    $this->render('adminPanel', ['users' => $users]);
}

    // Funkcja do usuwania użytkowników i ich danych
    public function deleteUser() {
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@admin.pl') {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['user_id'])) {
            $userId = $_POST['user_id'];

            try {
                // Usuń wszystkie dane użytkownika z tabeli anime_list
                $stmt = $this->conn->prepare("DELETE FROM anime_list WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                // Usuń użytkownika z tabeli users
                $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                header('Location: /adminPanel'); // Po usunięciu przekieruj z powrotem do panelu admina
                exit();
            } catch (PDOException $e) {
                echo "Błąd podczas usuwania użytkownika: " . $e->getMessage();
            }
        }
    }
 // Funkcja do pobierania bazy danych
// public function downloadDatabase() {
//     session_start();

//     if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@admin.pl') {
//         header('Location: /login');
//         exit();
//     }

//     // Ścieżka do kopii zapasowej bazy danych
//     $backupFile = '/tmp/backup.sql';

//     // Wykonaj polecenie do utworzenia kopii zapasowej i przekieruj błędy do pliku
//     $command = "PGPASSWORD='docker' pg_dump --username=docker --dbname=db --file=$backupFile 2>&1";

//     try {
//         // Wykonaj komendę i przechwyć wyjście
//         exec($command, $output, $resultCode);

//         // Sprawdź, czy komenda zakończyła się sukcesem
//         if ($resultCode !== 0) {
//             // Zapisz błędy do pliku .sql
//             file_put_contents($backupFile, implode("\n", $output), FILE_APPEND);
//         }

//         // Pobieranie pliku .sql
//         if (file_exists($backupFile)) {
//             header('Content-Description: File Transfer');
//             header('Content-Type: application/octet-stream');
//             header('Content-Disposition: attachment; filename=' . basename($backupFile));
//             header('Expires: 0');
//             header('Cache-Control: must-revalidate');
//             header('Pragma: public');
//             header('Content-Length: ' . filesize($backupFile));

//             // Odczyt pliku i jego przesłanie do przeglądarki
//             readfile($backupFile);
//             exit();
//         } else {
//             throw new Exception('Plik kopii zapasowej nie istnieje.');
//         }
//     } catch (Exception $e) {
//         echo "Błąd podczas tworzenia kopii zapasowej: " . $e->getMessage();
//     }
// }
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
            'message' => 'Wystąpił błąd podczas wykonywania dumpa bazy danych.',
            'details' => $output 
        ]);
    }
}

    
    
}

<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../models/User.php';

class SecurityController extends AppController {
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = new User();
            $isAuthenticated = $user->login($email, $password);

            if ($isAuthenticated) {
                session_start();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['email'] = $email;
                $userEmail = $_SESSION['email']; 
                if ($userEmail == 'admin@admin.pl'){
                    header('Location: /adminPanel');
                    exit();
                }
                // Przekierowanie na dashboard po poprawnym zalogowaniu
                header('Location: /dashboard');
                exit();
            } else {
                // Jeśli logowanie nieudane, wyświetl komunikat o błędzie
                $this->render('login', ['error' => 'Niepoprawny email lub hasło.']);
            }
        } else {
            $this->render('login');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $user = new User();
            $result = $user->register($name, $email, $password, $confirmPassword);
            
            if ($result === true) {
                // Przekierowanie na login po udanej rejestracji
                header('Location: /login');
                exit();
            } else {
                // Jeśli rejestracja nieudana, wyświetl komunikat o błędzie
                $this->render('register', ['error' => $result]);
            }
        } else {
            $this->render('register');
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        // Przekieruj na stronę główną
        header('Location: /login');
        exit();
    }
}

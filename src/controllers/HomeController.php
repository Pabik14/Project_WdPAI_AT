<?php

require_once 'AppController.php';

class HomeController extends AppController {
    public function home() {
        
        $this->render('home');
    }
}

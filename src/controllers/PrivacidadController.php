<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PrivacidadController {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }

    public function index() {
        echo $this->twig->render('privacidad.html.twig', [
            'user_id' => $_COOKIE['user_id']]);
    }
}

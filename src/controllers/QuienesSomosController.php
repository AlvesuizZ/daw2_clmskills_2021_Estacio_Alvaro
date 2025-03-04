<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class QuienesSomosController {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }

    public function index() {
        echo $this->twig->render('quienesSomos.html.twig', [
            'user_id' => $_COOKIE['user_id']
        ]);
    }
}

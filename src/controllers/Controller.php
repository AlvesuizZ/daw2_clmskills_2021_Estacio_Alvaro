<?php
namespace App\controllers;

class Controller
{
    public function index(){
        $this->render('home.php',[]);
    }

    public function render($view,$data=[]){
        extract($data);
        $viewPath = __DIR__ . "/../views/$view";
        if(file_exists($viewPath)){
            ob_start();
            require $viewPath;
            $contenido = ob_get_clean();
        }
        require __DIR__ . '/../views/plantilla.php';
    }
}

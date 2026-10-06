<?php
namespace App\Controller;

use App\Core\View;

class HomeController{

    public function __construct(
        private readonly View $view,

    )
    {}
    public function index()
    {
        return $this->view->render('home/index.html.twig');

    }
}
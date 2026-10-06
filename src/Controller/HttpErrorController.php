<?php

namespace App\Controller;

use App\Core\View;

class HttpErrorController{

    public function __construct(
        private readonly View $view
    ) {
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view->render('http_errors/error.html.twig', [
            'title' => '404 - Page not Found',
             'status' => '404',
            'message' => 'Page not Found',
        ]);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->view->render('http_errors/error.html.twig', [
            'title' => '403 - Forbidden',
            'status' => '403',
            'message' => 'You do not have permission to access this page.',
        ]);
    }

    public function internalServerError(): void
    {
        http_response_code(500);
        $this->view->render('http_errors/error.html.twig', [
            'title' => '500 - Internal Server Error',
            'status' => '500',
            'message' => 'An unexpected error occurred. Please try again later.',
        ]);
    }
}
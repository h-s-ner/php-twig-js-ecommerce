<?php
namespace App\Controller;

use App\Core\Auth;
use App\Core\View;
use App\Core\Middleware;
use App\Service\AuthService;

class AuthController{

    public function __construct(
        private readonly View $view,
        private readonly AuthService $authService
    )
    {}

    public function loginIndex(): void
    {

        $data = [
            'title' => 'Login',
            'errors' => [],
            'email' => '',

        ];

        $this->view->render('auth/login.html.twig', $data);
    }

    public function login(): void
    {
        $data = [
            'title' => 'Login',
            'errors' => [],
            'email' => '',

        ];
        if (isset($_POST['login']))
        {

            $response = $this->authService->login($_POST);
            if ($response === true)
            {
                $redirectUrl = $_SESSION['redirect_after_login'] ?? '/';
                if (Auth::isAdmin())
                {
                    $redirectUrl = '/admin';
                }
                header('Location: ' . $redirectUrl);
                exit;
            }
            $errors = $response['errors'];
            $data = [
                'title' => 'Login',
                'errors' => $errors,
                'email' => $response['email'] ?? '',

            ];
        }
        $this->view->render('auth/login.html.twig', $data);
    }
    public function registerIndex(): void
    {

        $data = [
            'title' => 'Register',
            'fname' => '',
            'lname' => '',
            'phone' => '',
            'email' => '',
            'errors' => [],
        ];
        $this->view->render('auth/register.html.twig', $data);
    }
    public function register(): void
    {
        $data = [
            'title' => 'Register',
            'fname' => '',
            'lname' => '',
            'phone' => '',
            'email' => '',
            'errors' => [],
        ];

        if (isset($_POST['register']))
        {

            $response = $this->authService->register($_POST);
            if (is_array($response))
            {
                $errors = $response['errors'];
                $oldValues = $response['oldValues'];

                $data = array_merge($data, [
                    'fname' => $oldValues['fname'],
                    'lname' => $oldValues['lname'],
                    'phone' => $oldValues['phone'],
                    'email' => $oldValues['email'],
                    'errors' => $errors,
                ]);
            }
            else
            {

                header('Location: /login');
                exit;
            }
        }
        $this->view->render('auth/register.html.twig', $data);
    }
    public function logout():void
    {
        $this->authService->logout();
    }
}
?>

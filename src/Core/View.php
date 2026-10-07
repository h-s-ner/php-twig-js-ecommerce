<?php
namespace App\Core;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

class View
{
    private Environment $twig;

    public function __construct()
    {
        // Twig-Loader für die Templates einrichten
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader,[
            'cache' => false,
            'debug' => true,
        ]);
        $this->twig->addGlobal('role', Auth::getRole());
        $this->addCustomFunctions();
    }
    // Template rendern und Daten übergeben
    public function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }

    private function addCustomFunctions()
    {
        // csrf_token
        $this->twig->addFunction(new TwigFunction(
            'csrf_input',
            function (string $key): string {
                $token = Csrf::getCsrfToken($key);
                return sprintf(
                    '<input type="hidden" name="csrf_token" value="%s">',
                    htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
                );
        },
        ['is_safe' => ['html']]
        ));
    }
}
?>

<?php
namespace App\Core;

use Twig\Environment;
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
    }
    // Template rendern und Daten übergeben
    public function render(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }
}
?>

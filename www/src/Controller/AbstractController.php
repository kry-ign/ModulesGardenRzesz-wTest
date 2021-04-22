<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    protected ?Environment $twig = null;

    public function __construct()
    {
        $this->twig = new Environment(new FilesystemLoader("../src/View"), array("cache" => false));
    }

    public function redirect(string $page, array $params = []): void
    {
        $params["access"] = $page;
        header("Location: index.php?" . http_build_query($params));

        exit;
    }
}
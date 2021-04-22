<?php

declare(strict_types=1);

namespace App;

class Router
{
    private const DEFAULT_PATH = "App\Controller\\";
    private const DEFAULT_CONTROLLER = "AccountController";
    private const DEFAULT_METHOD = "list";

    private $controller = self::DEFAULT_CONTROLLER;
    private string $method = self::DEFAULT_METHOD;

    public function __construct()
    {
        $this->parseUrl();
        $this->setController();
        $this->setMethod();
    }

    private function parseUrl(): void
    {
        $access = filter_input(INPUT_GET, "access");

        if (!isset($access)) {
            $access = "home";
        }

        $access = explode("!", $access);
        $this->controller = $access[0];
        $this->method = count($access) == 1 ? "default" : $access[1];
    }

    public function setController(): void
    {
        $this->controller = ucfirst(strtolower($this->controller)) . "Controller";
        $this->controller = self::DEFAULT_PATH . $this->controller;

        if (!class_exists($this->controller)) {
            $this->controller = self::DEFAULT_PATH . self::DEFAULT_CONTROLLER;
        }
    }

    public function setMethod(): void
    {
        $this->method = strtolower($this->method);

        if (!method_exists($this->controller, $this->method)) {
            $this->method = self::DEFAULT_METHOD;
        }
    }

    public function run(): void
    {
        $this->controller = new $this->controller();
        $response = call_user_func([$this->controller, $this->method]);

        echo filter_var($response);
    }
}
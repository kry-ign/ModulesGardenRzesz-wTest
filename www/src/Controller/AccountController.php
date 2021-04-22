<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DirectAdminClient;
use App\ValueObject\Admin;
use App\ValueObject\User;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AccountController extends AbstractController
{
    /**
     * Renders the View Home
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(): string
    {
        $directAdminClient = new DirectAdminClient();

        $accounts = $directAdminClient->getAllAccounts();

        return $this->twig->render("home.twig", ["accounts" => $accounts]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create()
    {
        $directAdminClient = new DirectAdminClient();
        if (empty($_POST)) {
            return $this->twig->render("create/index.twig");
        }

        if (htmlspecialchars($_POST["role"]) === 'admin') {
            $directAdminClient->createAccount(
                new Admin(
                    htmlspecialchars($_POST["username"]),
                    htmlspecialchars($_POST["email"]),
                    htmlspecialchars($_POST["password"]),
                    $_POST["notify"],
                )
            );
        } elseif (htmlspecialchars($_POST["role"]) === 'user') {
            $directAdminClient->createAccount(
                new User(
                    htmlspecialchars($_POST["username"]),
                    htmlspecialchars($_POST["email"]),
                    htmlspecialchars($_POST["password"]),
                    $_POST["notify"],
                    htmlspecialchars($_POST["domain"]),
                )
            );
        }

        return $this->list();
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function delete()
    {
        $directAdminClient = new DirectAdminClient();
        if (empty($_POST)) {
            return $this->twig->render("create/index.twig");
        }

        if (!empty($_POST["username"])) {
            $directAdminClient->delete($_POST["username"]);
        }

        return $this->list();
    }
}
<?php

namespace App\Controller;

use App\Service\DirectAdminClient;
use App\ValueObject\Admin;
use App\ValueObject\User;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends AbstractController
{
    /**
     * Renders the View Home
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $test = new DirectAdminClient();

        $accounts = $test->getAdmins();
        $test->createAccount(
          new Admin(
              'krisowy23',
              'lr2o4@wp.pl',
              'kasdha',
              '34534',

          )
        );

        var_dump($accounts);


        return $this->twig->render("home.twig", ["accounts" => []]);
    }

    public function createAccount(): Response
    {
        $test = new DirectAdminClient();

        $account= $test->getUsers();

    }
}
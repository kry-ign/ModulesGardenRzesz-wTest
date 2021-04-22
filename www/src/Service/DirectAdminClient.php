<?php

namespace App\Service;

use App\ValueObject\AbstractAccount;
use App\ValueObject\Admin;
use App\ValueObject\Reseller;
use App\ValueObject\User;
use App\ValueObject\View\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class DirectAdminClient
{
    private const CMD_API_ACCOUNT_ADMIN = 'CMD_API_ACCOUNT_ADMIN';
    private const CMD_API_ACCOUNT_RESELLER = 'CMD_API_ACCOUNT_RESELLER';
    private const CMD_API_ACCOUNT_USER = 'CMD_API_ACCOUNT_USER';

    private const CMD_API_SELECT_USERS = 'CMD_API_SELECT_USERS';

    private const CMD_API_SHOW_ALL_USERS = 'CMD_API_SHOW_ALL_USERS';
    private const CMD_API_SHOW_ADMINS = 'CMD_API_SHOW_ADMINS';

    private const CMD_API_MODIFY_USER = 'CMD_API_MODIFY_USER';

    private const CMD_API_CHANGE_INFO = 'CMD_API_CHANGE_INFO';

    private Client $client;
    private ?ResponseInterface $response;
    private string $login;
    private string $password;
    private string $endpoint;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => ('http://157.90.249.58:2222'),
            ]
        );
        $this->login = 'admin';
        $this->password = '0tOoS^9HuyW2Kb';
        $this->response = null;
    }

    public function getAdmins(): array
    {
        $admins = [];
        $this->endpoint = self::CMD_API_SHOW_ADMINS;
        $this->sendRequest('GET');

        foreach ($this->getAccountsNamesListFromRequest() as $accountName) {
            $admins[] = Account::createAdmin($accountName);
        }

        return $admins;
    }

    public function getUsers(): array
    {
        $users = [];
        $this->endpoint = self::CMD_API_SHOW_ALL_USERS;
        $this->sendRequest('GET');

        foreach ($this->getAccountsNamesListFromRequest() as $accountName) {
            $users[] = Account::createUser($accountName);
        }

        return $users;
    }

    private function getAccountsNamesListFromRequest(): array
    {
        $response = [];
        $bodyContents = $this->response->getBody()->getContents();
        parse_str($bodyContents, $response);

        return $response['list'];
    }

    public function getAllAccounts(): array
    {
        return array_merge($this->getUsers(), $this->getAdmins());
    }

    public function createAccount(
        AbstractAccount $account
    ): ResponseInterface
    {
        $params = $account->getParams();
        $params['action'] = 'create';
        $params['add'] = 'Submit';

        $accountClass = get_class($account);

        switch ($accountClass) {
            case Admin::class:
                $this->endpoint = self::CMD_API_ACCOUNT_ADMIN;
                break;
            case Reseller::class:
                $this->endpoint = self::CMD_API_ACCOUNT_RESELLER;
                break;
            case User::class:
                $this->endpoint = self::CMD_API_ACCOUNT_USER;
                break;
        }

        $this->sendRequest('POST', $params);

        return $this->response;
    }

    public function editUserSettings(
        string $username,
        string $bandwidth,
        string $quota
    ): ResponseInterface
    {
        $this->endpoint = self::CMD_API_MODIFY_USER;
        $this->sendRequest(
            'POST',
            [
                'additional_bandwidth' => $bandwidth,
                'quota' => $quota,
                'additional_bw' => 'anything',
                'action' => 'single',
                'user' => $username
            ]
        );

        return $this->response;
    }

    public function editUserInfo(
        string $oldEmail,
        string $email
    ): ResponseInterface
    {
        $this->endpoint = self::CMD_API_CHANGE_INFO;
        $this->sendRequest(
            'POST',
            [
                'evalue' => $email,
                'email' => $oldEmail
            ]
        );

        return $this->response;
    }

    public function delete(string $username): ResponseInterface
    {
        $this->endpoint = self::CMD_API_SELECT_USERS;
        $this->sendRequest(
            'POST',
            [
                'confirmed' => 'Confirm',
                'delete' => 'yes',
                'select0' => $username
            ]
        );

        return $this->response;
    }

    public function sendRequest(string $method, array $params = []): void
    {
        if (!$this->endpoint) {
            throw new \Exception('Missing endpoint');
        }

        try {
            $this->response = $this->client->request(
                $method,
                '/' . $this->endpoint,
                [
                    'query' => $params,
                    'auth' => [
                        $this->login,
                        $this->password
                    ]
                ]);
        } catch (GuzzleException $e) {
            echo (string) $e;
        }

        $this->validateResponse();
    }

    protected function validateResponse(): void
    {
        $contentType = $this->response->getHeaderLine('Content-Type');
        $header = strtolower($this->response->getHeaderLine('X-DirectAdmin'));
        if ($contentType === 'text/html'
            && $header === 'unauthorized'
        ) {
            throw new \Exception(
                'Invalid credentials!'
            );
        }

        if ($contentType !== 'text/plain') {
            throw new \Exception('Not valid response format', 0, null, $this->response);
        }
        $body = $this->response->getBody();
        $body->seek(0);
        $bodyContents = $body->getContents();
        $body->seek(0);

        if (substr($bodyContents, 0, 6) === 'error='
            && $bodyContents[6] !== '0'
            && substr($bodyContents, 6, 3) !== '%30'
        ) {
            $data = [];
            parse_str($this->decodeResponse($bodyContents), $data);
            $body->seek(0);
            throw new \Exception(
                'Unknown error! ' . $bodyContents,
                0,
                null
            );
        }
    }

    protected function decodeResponse(string $decodedResponse): string
    {
        $test =  preg_replace_callback(
            '/&#([0-9]{2})/',
            static function ($val) {
                return chr($val[1]);
            },
            $decodedResponse
        );

        var_dump($test);
        return $test;
    }
}
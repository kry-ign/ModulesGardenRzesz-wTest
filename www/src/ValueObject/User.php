<?php

namespace App\ValueObject;

class User extends AbstractAccount
{
    private string $apiDomain;
    private string $ip;

    public function __construct(
        $username,
        $email,
        $password,
        $notify,
        $apiDomain = 'test-domain.com',
        $ip = '157.90.249.58'
    )
    {
        parent::__construct($username, $email, $password, $notify);

        $this->apiDomain = $apiDomain;
        $this->ip = $ip;
    }

    public function getParams(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'passwd' => $this->password,
            'passwd2' => $this->password,
            'notify' => $this->notify ? 'yes' : 'no',
            'domain' => $this->apiDomain,
            'ip' => $this->ip
        ];
    }
}
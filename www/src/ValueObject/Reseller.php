<?php

declare(strict_types=1);

namespace App\ValueObject;

class Reseller extends AbstractAccount
{
    private string $apiDomain;
    private string $ip;

    public function __construct($username, $email, $password, $notify, $apiDomain, $ip)
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
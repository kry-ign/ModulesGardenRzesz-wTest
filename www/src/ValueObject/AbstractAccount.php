<?php

declare(strict_types=1);

namespace App\ValueObject;

abstract class AbstractAccount
{
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $notify;

    public function __construct(
        string $username,
        string $email,
        string $password,
        bool $notify
    )
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->notify = $notify;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getNotify(): bool
    {
        return $this->notify;
    }

    public function getParams(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'passwd' => $this->password,
            'passwd2' => $this->password,
            'notify' => $this->notify ? 'yes' : 'no'
        ];
    }
}
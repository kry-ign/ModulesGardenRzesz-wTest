<?php

declare(strict_types=1);

namespace App\ValueObject\View;

class Account
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    protected string $username;
    protected string $role;

    public function __construct(
        string $username,
        string $role
    )
    {
        $this->username = $username;
        $this->role = $role;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public static function createAdmin(string $username): self
    {
        return new self($username, self::ROLE_ADMIN);
    }

    public static function createUser(string $username): self
    {
        return new self($username, self::ROLE_USER);
    }
}
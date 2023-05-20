<?php

declare(strict_types=1);

namespace TaylorR\MeetBard\security;

class User
{

    private string $SNlM0e = "";

    public function __construct(
        private string $token
    ){}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getSNlM0e(): string
    {
        return $this->SNlM0e;
    }

    public function setSNlM0e(string $SNlM0e): void
    {
        $this->SNlM0e = $SNlM0e;
    }
}
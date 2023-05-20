<?php

declare(strict_types=1);

namespace TaylorR\MeetBard\security;

class User
{

    public function __construct(
        private string $token
    ){}

    public function getToken(): string
    {
        return $this->token;
    }
}
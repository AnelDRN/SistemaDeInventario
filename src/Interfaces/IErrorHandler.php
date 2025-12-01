<?php

namespace App\Interfaces;

interface IErrorHandler
{
    public function logError(string $message, array $context = []): void;
}

<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IErrorHandler
{
    public function logError(string $message, array $context = []): void;
}
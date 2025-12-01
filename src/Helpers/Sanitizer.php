<?php

namespace App\Helpers;

class Sanitizer
{
    /**
     * Sanitizes a string, removing HTML tags and encoding special characters.
     *
     * @param string $input The string to sanitize.
     * @return string The sanitized string.
     */
    public static function sanitizeString(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizes an integer input.
     *
     * @param mixed $input The input to sanitize.
     * @return int|null The sanitized integer, or null if invalid.
     */
    public static function sanitizeInt(mixed $input): ?int
    {
        $filtered = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        return ($filtered !== false && $filtered !== null && $filtered !== '') ? (int)$filtered : null;
    }

    /**
     * Sanitizes a float input.
     *
     * @param mixed $input The input to sanitize.
     * @return float|null The sanitized float, or null if invalid.
     */
    public static function sanitizeFloat(mixed $input): ?float
    {
        $filtered = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return ($filtered !== false && $filtered !== null && $filtered !== '') ? (float)$filtered : null;
    }

    /**
     * Validates if a string is a valid email address.
     *
     * @param string $email The email string to validate.
     * @return bool True if the email is valid, false otherwise.
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates if a string is a valid URL.
     *
     * @param string $url The URL string to validate.
     * @return bool True if the URL is valid, false otherwise.
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validates if a string represents a valid date in 'YYYY-MM-DD' format.
     *
     * @param string $date The date string to validate.
     * @return bool True if the date is valid, false otherwise.
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Sanitizes an array of inputs recursively.
     *
     * @param array $input The array to sanitize.
     * @return array The sanitized array.
     */
    public static function sanitizeArray(array $input): array
    {
        $sanitizedArray = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitizedArray[self::sanitizeString($key)] = self::sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitizedArray[self::sanitizeString($key)] = self::sanitizeString($value);
            } elseif (is_int($value)) {
                $sanitizedArray[self::sanitizeString($key)] = self::sanitizeInt($value);
            } elseif (is_float($value)) {
                $sanitizedArray[self::sanitizeString($key)] = self::sanitizeFloat($value);
            } else {
                $sanitizedArray[self::sanitizeString($key)] = $value;
            }
        }
        return $sanitizedArray;
    }
}

<?php

namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function validateEmail(string $email, string $field = 'email'): bool
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email';
            return false;
        }
        return true;
    }

    public function validatePassword(string $password, string $field = 'password'): bool
    {
        if (empty($password) || strlen($password) < 6) {
            $this->errors[$field] = 'Password must be at least 6 characters';
            return false;
        }
        return true;
    }

    public function validateRequired(string $value, string $field = 'field'): bool
    {
        if (empty($value)) {
            $this->errors[$field] = ucfirst($field) . ' is required';
            return false;
        }
        return true;
    }

    public function validateLength(string $value, int $min, int $max, string $field = 'field'): bool
    {
        $length = strlen($value);

        if ($length < $min || $length > $max) {
            $this->errors[$field] = ucfirst($field) . " must be between {$min} and {$max} characters";
            return false;
        }
        return true;
    }

    public function validateInteger(string $value, string $field = 'field'): bool
    {
        if (!is_numeric($value) || (int)$value != $value) {
            $this->errors[$field] = ucfirst($field) . ' must be an integer';
            return false;
        }

        return true;
    }

    public function validatePhone(string $phone, string $field = 'phone'): bool
    {
        if (empty($phone)) {
            $this->errors[$field] = 'Phone is required';
            return false;
        }

        // normalize
        $cleaned = preg_replace('/[^0-9+\-\s]/', '', $phone);

        if (strlen($cleaned) < 10) {
            $this->errors[$field] = 'Invalid phone number';
            return false;
        }

        return true;
    }

    public function validateEnum(string $value, array $allowedValues, string $field = 'field'): bool
    {
        if (!in_array($value, $allowedValues)) {
            $this->errors[$field] = ucfirst($field) . ' must be one of: ' . implode(', ', $allowedValues);
            return false;
        }

        return true;
    }

    public function validateUrl(string $url, string $field = 'url'): bool
    {
        if (empty($url)) {
            $this->errors[$field] = 'URL is required';
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = 'Invalid URL format';
            return false;
        }

        return true;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }
}

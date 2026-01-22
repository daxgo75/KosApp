<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !preg_match('/[a-z]/', $value) ||      // At least one lowercase
            !preg_match('/[A-Z]/', $value) ||      // At least one uppercase
            !preg_match('/[0-9]/', $value) ||      // At least one number
            !preg_match('/[!@#$%^&*]/', $value)    // At least one special char
        ) {
            $fail('Password must contain uppercase, lowercase, number, and special character (!@#$%^&*)');
        }
    }
}

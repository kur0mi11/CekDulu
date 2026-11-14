<?php

namespace App\Rules;

use App\Support\Phone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Phone::isValid((string) $value)) {
            $fail('Nomor telepon tidak valid. Gunakan nomor seluler Indonesia, contoh 081234567890 atau +6281234567890.');
        }
    }
}
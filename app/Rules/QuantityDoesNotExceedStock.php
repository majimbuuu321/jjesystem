<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class QuantityDoesNotExceedStock implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */

    public function __construct(protected int $stock) {}
    
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
         if ($value > $this->stock) {
            $fail("Quantity cannot exceed available stock ({$this->stock}).");
        }
    }
}

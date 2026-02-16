<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class SumsToHundred implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sum = (data_get($this->data, "data.attributes.assignmentPercent") ?? 0) +
            (data_get($this->data, "data.attributes.quizPercent") ?? 0) +
            (data_get($this->data, "data.attributes.midPercent") ?? 0) +
            (data_get($this->data, "data.attributes.finalPercent") ?? 0);

        if ($sum != 100)
        {
            $fail("The total percentage must equal 100%. (Current: {$sum}%)");
        }
    }
}

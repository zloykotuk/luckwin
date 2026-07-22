<?php

namespace App\Http\Requests;

use App\Helper\PhoneNormalizer;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(UserRepository $users): array
    {
        return [
            'username' => [
                'bail',
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9]+$/',
                function (string $attribute, string $value, Closure $fail) use ($users): void {
                    if ($users->exists(fn (Builder $query) => $query->where('username', $value))) {
                        $fail('This username has already been taken.');
                    }
                },
            ],
            'phonenumber' => [
                'bail',
                'required',
                'string',
                'max:20',
                (new Phone)->international(),
                function (string $attribute, string $value, Closure $fail) use ($users): void {
                    $normalized = PhoneNormalizer::normalize($value);

                    if ($users->exists(fn (Builder $query) => $query->where('phonenumber', $normalized))) {
                        $fail('This phone number is already registered.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Only English letters and digits are allowed.',
            'phonenumber.phone' => 'Enter a valid phone number, including the country code.',
        ];
    }
}

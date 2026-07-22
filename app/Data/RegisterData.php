<?php

namespace App\Data;

use App\Http\Requests\RegisterRequest;

final readonly class RegisterData
{
    public function __construct(
        public string $username,
        public string $phonenumber,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            username: $request->string('username')->toString(),
            phonenumber: $request->string('phonenumber')->toString(),
        );
    }
}

<?php

namespace App\Services;

use App\Data\RegisterData;
use App\Models\Link;
use App\Repositories\UserRepository;

final readonly class RegistrationService
{
    public function __construct(private UserRepository $users) {}

    public function register(RegisterData $data): Link
    {
        return $this->users->create($data);
    }
}

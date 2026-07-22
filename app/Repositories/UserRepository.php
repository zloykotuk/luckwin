<?php

namespace App\Repositories;

use App\Data\RegisterData;
use App\Helper\PhoneNormalizer;
use App\Models\Link;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class UserRepository
{
    public function __construct(private readonly LinkRepository $links) {}

    /**
     * Create a user together with their first lucky link, atomically.
     */
    public function create(RegisterData $data): Link
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data->username,
                'phonenumber' => PhoneNormalizer::normalize($data->phonenumber),
            ]);

            return $this->links->createForUserId($user->id);
        });
    }

    /**
     * @param  Closure(Builder<User>): Builder<User>  $scope
     */
    public function exists(Closure $scope): bool
    {
        return $scope(User::query())->exists();
    }
}

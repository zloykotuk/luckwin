<?php

namespace App\Repositories;

use App\Models\Link;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class LinkRepository
{
    /**
     * How many random hashes to try before giving up. Collisions on a 32-char
     * random string are astronomically unlikely; this only guards against them,
     * it never runs an existence check up front.
     */
    private const MAX_HASH_ATTEMPTS = 5;

    public function createForUserId(int $userId): Link
    {
        return $this->withUniqueHash(
            fn (string $hash) => Link::create(['user_id' => $userId, 'hash' => $hash]),
        );
    }

    public function regenerateHash(string $hash): Link
    {
        return $this->withUniqueHash(
            fn (string $newHash) => tap(Link::where('hash', $hash)->firstOrFail())->update(['hash' => $newHash]),
        );
    }

    public function deactivate(string $hash): void
    {
        Link::where('hash', $hash)->firstOrFail()->delete();
    }

    public function userIdForHash(string $hash): int
    {
        return Link::where('hash', $hash)->firstOrFail()->user_id;
    }

    /**
     * Insert/update with a freshly generated hash, retrying only when the database's
     * own unique constraint actually rejects a collision (via a savepoint, so a
     * retry never aborts an outer transaction the caller may be running in).
     *
     * @param  Closure(string): Link  $attempt
     */
    private function withUniqueHash(Closure $attempt): Link
    {
        for ($attemptNumber = 1; $attemptNumber <= self::MAX_HASH_ATTEMPTS; $attemptNumber++) {
            try {
                return DB::transaction(fn () => $attempt(Str::random(32)));
            } catch (UniqueConstraintViolationException $e) {
                if ($attemptNumber === self::MAX_HASH_ATTEMPTS) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Unreachable: the loop above always returns or throws.');
    }
}

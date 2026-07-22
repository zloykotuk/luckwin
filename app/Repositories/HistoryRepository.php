<?php

namespace App\Repositories;

use App\Models\History;
use Illuminate\Database\Eloquent\Collection;

final class HistoryRepository
{
    public function create(int $userId, bool $result, float $payout): History
    {
        return History::create([
            'user_id' => $userId,
            'result' => $result,
            'payout' => $payout,
        ]);
    }

    /**
     * @return Collection<int, History>
     */
    public function forUser(int $userId, int $limit = 3): Collection
    {
        return History::where('user_id', $userId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}

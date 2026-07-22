<?php

namespace App\Models;

use Database\Factories\HistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $result
 * @property string $payout
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'result', 'payout'])]
class History extends Model
{
    /** @use HasFactory<HistoryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'result' => 'boolean',
            'payout' => 'decimal:2',
        ];
    }
}

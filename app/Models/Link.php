<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $hash
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['user_id', 'hash'])]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory, SoftDeletes;

    /**
     * A link is only reachable for this many days after it was created.
     */
    public const EXPIRES_AFTER_DAYS = 7;

    /**
     * Routes resolve links by their hash instead of the numeric id.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    /**
     * Route binding excludes links older than EXPIRES_AFTER_DAYS, so an expired
     * hash 404s exactly like an unknown or soft-deleted one.
     *
     * @param  Builder<static>  $query
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Builder<static>
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->where('created_at', '>=', now()->subDays(self::EXPIRES_AFTER_DAYS));
    }
}

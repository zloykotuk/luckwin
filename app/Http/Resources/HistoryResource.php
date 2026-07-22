<?php

namespace App\Http\Resources;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin History
 */
class HistoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'result' => $this->result,
            'payout' => (float) $this->payout,
        ];
    }
}

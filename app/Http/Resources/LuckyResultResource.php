<?php

namespace App\Http\Resources;

use App\Data\LuckyOutcome;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LuckyOutcome
 */
class LuckyResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'roll' => $this->roll,
            'result' => $this->result,
            'payout' => $this->payout,
        ];
    }
}

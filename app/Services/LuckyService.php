<?php

namespace App\Services;

use App\Data\LinkHashData;
use App\Data\LuckyOutcome;
use App\Repositories\HistoryRepository;
use App\Repositories\LinkRepository;

final readonly class LuckyService
{
    private const MIN_ROLL = 1;

    private const MAX_ROLL = 1000;

    public function __construct(
        private LinkRepository $links,
        private HistoryRepository $history,
    ) {}

    public function play(LinkHashData $data): LuckyOutcome
    {
        $outcome = $this->outcomeForRoll($this->roll());

        $this->history->create(
            $this->links->userIdForHash($data->hash),
            $outcome->result,
            $outcome->payout,
        );

        return $outcome;
    }

    private function roll(): int
    {
        return random_int(self::MIN_ROLL, self::MAX_ROLL);
    }

    /**
     * Exposed separately from play() so the win/lose and payout rules
     * can be tested with fixed rolls instead of relying on randomness.
     */
    public function outcomeForRoll(int $roll): LuckyOutcome
    {
        $result = $roll % 2 === 0;

        return new LuckyOutcome(
            roll: $roll,
            result: $result,
            payout: $result ? $this->payoutFor($roll) : 0.0,
        );
    }

    private function payoutFor(int $roll): float
    {
        $percentage = match (true) {
            $roll > 900 => 0.70,
            $roll > 600 => 0.50,
            $roll > 300 => 0.30,
            default => 0.10,
        };

        return round($roll * $percentage, 2);
    }
}

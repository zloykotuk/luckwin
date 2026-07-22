<?php

namespace App\Data;

final readonly class LuckyOutcome
{
    public function __construct(
        public int $roll,
        public bool $result,
        public float $payout,
    ) {}
}

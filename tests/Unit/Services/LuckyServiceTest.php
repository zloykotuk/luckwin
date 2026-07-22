<?php

use App\Repositories\HistoryRepository;
use App\Repositories\LinkRepository;
use App\Services\LuckyService;

function luckyService(): LuckyService
{
    return new LuckyService(new LinkRepository, new HistoryRepository);
}

it('wins on even rolls and loses on odd rolls', function () {
    expect(luckyService()->outcomeForRoll(2)->result)->toBeTrue();
    expect(luckyService()->outcomeForRoll(1000)->result)->toBeTrue();
    expect(luckyService()->outcomeForRoll(1)->result)->toBeFalse();
    expect(luckyService()->outcomeForRoll(999)->result)->toBeFalse();
});

it('pays nothing on a loss', function () {
    expect(luckyService()->outcomeForRoll(999)->payout)->toBe(0.0);
});

it('pays 70% of the roll above 900', function () {
    expect(luckyService()->outcomeForRoll(942)->payout)->toBe(659.4);
});

it('pays 50% of the roll above 600', function () {
    expect(luckyService()->outcomeForRoll(700)->payout)->toBe(350.0);
});

it('pays 30% of the roll above 300', function () {
    expect(luckyService()->outcomeForRoll(400)->payout)->toBe(120.0);
});

it('pays 10% of the roll at or below 300', function () {
    expect(luckyService()->outcomeForRoll(300)->payout)->toBe(30.0);
    expect(luckyService()->outcomeForRoll(2)->payout)->toBe(0.2);
});

<?php

use App\Models\History;
use App\Models\Link;

it('plays lucky and records the outcome in history', function () {
    $link = Link::factory()->create();

    $response = $this->postJson("/a/{$link->hash}/lucky");

    $response->assertOk();
    $response->assertJsonStructure(['data' => ['roll', 'result', 'payout']]);

    $roll = $response->json('data.roll');
    $result = $response->json('data.result');
    $payout = $response->json('data.payout');

    expect($roll)->toBeGreaterThanOrEqual(1)->toBeLessThanOrEqual(1000);
    expect($result)->toBe($roll % 2 === 0);
    expect((float) $payout)->toBe($result ? (float) $payout : 0.0);

    $this->assertDatabaseCount('histories', 1);
    $this->assertDatabaseHas('histories', [
        'user_id' => $link->user_id,
        'result' => $result,
    ]);
});

it('returns only the latest three history entries, newest first', function () {
    $link = Link::factory()->create();

    $entries = History::factory()->count(5)->create(['user_id' => $link->user_id]);

    $response = $this->getJson("/a/{$link->hash}/history");

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(3);
    expect((float) $data[0]['payout'])->toBe((float) $entries->last()->payout);
    expect((float) $data[2]['payout'])->toBe((float) $entries[2]->payout);
});

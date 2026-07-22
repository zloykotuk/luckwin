<?php

use App\Models\Link;
use App\Models\User;
use App\Repositories\LinkRepository;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('gives up after repeatedly colliding on the same hash', function () {
    $collidingHash = str_repeat('a', 32);
    Link::factory()->create(['hash' => $collidingHash]);

    Str::createRandomStringsUsing(fn () => $collidingHash);

    try {
        expect(fn () => (new LinkRepository)->createForUserId(User::factory()->create()->id))
            ->toThrow(UniqueConstraintViolationException::class);
    } finally {
        Str::createRandomStringsNormally();
    }
});

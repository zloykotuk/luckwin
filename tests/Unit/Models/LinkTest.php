<?php

use App\Models\Link;

it('resolves its route key as the hash, not the id', function () {
    $link = new Link(['hash' => 'abc123']);

    expect($link->getRouteKeyName())->toBe('hash');
    expect($link->getRouteKey())->toBe('abc123');
});

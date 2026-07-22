<?php

use App\Models\Link;

it('renders page A with the link hash and url', function () {
    $link = Link::factory()->create();

    $this->get("/a/{$link->hash}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('PageA')
            ->where('hash', $link->hash)
            ->where('url', route('links.show', $link))
        );
});

it('404s when visiting page A with an unknown hash', function () {
    $this->get('/a/does-not-exist')->assertNotFound();
});

it('regenerates the hash for the same link', function () {
    $link = Link::factory()->create();

    $response = $this->patchJson("/a/{$link->hash}/regenerate");

    $response->assertOk();
    $newHash = $response->json('data.hash');

    expect($newHash)->not->toBe($link->hash);

    $this->assertDatabaseHas('links', [
        'id' => $link->id,
        'hash' => $newHash,
        'user_id' => $link->user_id,
    ]);
});

it('soft deletes the link on deactivate and 404s on it afterwards', function () {
    $link = Link::factory()->create();

    $this->deleteJson("/a/{$link->hash}/delete")->assertNoContent();

    $this->assertSoftDeleted('links', ['id' => $link->id]);
    $this->getJson("/a/{$link->hash}/history")->assertNotFound();
});

it('returns 404 for an unknown hash', function () {
    $this->patchJson('/a/does-not-exist/regenerate')->assertNotFound();
    $this->deleteJson('/a/does-not-exist/delete')->assertNotFound();
    $this->postJson('/a/does-not-exist/lucky')->assertNotFound();
    $this->getJson('/a/does-not-exist/history')->assertNotFound();
});

it('404s on a link older than 7 days', function () {
    $link = Link::factory()->create();
    $link->created_at = now()->subDays(8);
    $link->save();

    $this->get("/a/{$link->hash}")->assertNotFound();
    $this->patchJson("/a/{$link->hash}/regenerate")->assertNotFound();
    $this->deleteJson("/a/{$link->hash}/delete")->assertNotFound();
    $this->postJson("/a/{$link->hash}/lucky")->assertNotFound();
    $this->getJson("/a/{$link->hash}/history")->assertNotFound();
});

it('stays reachable one second before the 7-day expiry', function () {
    $link = Link::factory()->create();
    $link->created_at = now()->subDays(7)->addSecond();
    $link->save();

    $this->get("/a/{$link->hash}")->assertOk();
});

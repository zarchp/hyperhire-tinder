<?php

use App\Models\User;
use App\Models\Swipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actor = User::factory()->create([
        'name' => 'Test Actor',
        'id' => 101,
    ]);

    $this->targetA = User::factory()->create(['name' => 'Target A']);
    $this->targetB = User::factory()->create(['name' => 'Target B']);
    $this->targetC = User::factory()->create(['name' => 'Target C']);

    $this->headers = ['X-User-Id' => $this->actor->id];
});

test('actor can successfully like a person', function () {
    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetA->id,
        'type' => 'like',
    ], $this->headers);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Swipe recorded successfully.',
            'is_match' => false,
        ]);

    $this->assertDatabaseHas('swipes', [
        'actor_user_id' => $this->actor->id,
        'target_user_id' => $this->targetA->id,
        'type' => 'like',
    ]);
});

test('actor can successfully dislike a person', function () {
    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetB->id,
        'type' => 'dislike',
    ], $this->headers);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Swipe recorded successfully.',
            'is_match' => false,
        ]);
});

test('swipe fails with invalid validation data', function () {
    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => 9999,
        'type' => 'invalid',
    ], $this->headers);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['target_user_id', 'type']);
});

test('swipe fails when target id is actor id', function () {
    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->actor->id,
        'type' => 'like',
    ], $this->headers);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Cannot swipe on yourself.',
        ]);
});

test('duplicate swipe fails', function () {
    $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetC->id,
        'type' => 'like',
    ], $this->headers)->assertStatus(201);

    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetC->id,
        'type' => 'dislike',
    ], $this->headers);

    $response->assertStatus(409)
        ->assertJson(['message' => 'Already swiped on this person.']);
});

test('swipe triggers mutual like and is_match is true', function () {
    Swipe::create([
        'actor_user_id' => $this->targetA->id,
        'target_user_id' => $this->actor->id,
        'type' => 'like',
    ]);

    $response = $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetA->id,
        'type' => 'like',
    ], $this->headers);

    $response->assertStatus(201)
        ->assertJson(['is_match' => true]);
});

test('recommendations list excludes already swiped users', function () {
    $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetA->id,
        'type' => 'like',
    ], $this->headers);

    $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetB->id,
        'type' => 'dislike',
    ], $this->headers);

    $response = $this->getJson('/api/people', $this->headers);

    $response->assertStatus(200)
        ->assertJsonMissing(['name' => $this->targetA->name])
        ->assertJsonMissing(['name' => $this->targetB->name])
        ->assertJsonFragment(['name' => $this->targetC->name]);
});

test('recommendations list is paginated', function () {
    User::factory(30)->create();

    $response = $this->getJson('/api/people', $this->headers);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ])
        ->assertJsonCount(10, 'data');
});

test('liked people list only returns users the actor liked', function () {
    $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetA->id,
        'type' => 'like',
    ], $this->headers);

    $this->postJson('/api/people/swipes', [
        'target_user_id' => $this->targetB->id,
        'type' => 'dislike',
    ], $this->headers);

    $response = $this->getJson('/api/people/liked', $this->headers);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => $this->targetA->name])
        ->assertJsonMissing(['name' => $this->targetB->name])
        ->assertJsonCount(1, 'data');
});

test('actor can successfully undo the last swipe action', function () {
    $this->postJson(
        '/api/people/swipes',
        ['target_user_id' => $this->targetA->id, 'type' => 'like'],
        $this->headers
    );
    $this->postJson(
        '/api/people/swipes',
        ['target_user_id' => $this->targetB->id, 'type' => 'dislike'],
        $this->headers
    );

    $this->assertDatabaseCount('swipes', 2);

    $response = $this->deleteJson('/api/people/undo-swipes', [], $this->headers);

    $response->assertStatus(204);
});

test('undo swipe returns 404 when no swipes exist', function () {
    $this->deleteJson('/api/people/undo-swipes', [], $this->headers);
    $this->deleteJson('/api/people/undo-swipes', [], $this->headers);

    $response = $this->deleteJson('/api/people/undo-swipes', [], $this->headers);

    $response->assertStatus(404);
});

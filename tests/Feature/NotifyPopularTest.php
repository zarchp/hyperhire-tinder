<?php

use App\Models\User;
use App\Models\Swipe;
use App\Mail\PopularUserMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Scheduling\Schedule;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->likers = User::factory(55)->create();

    $this->popularUser = User::factory()->create([
        'name' => 'Popular Test User',
        'is_popular_notified' => false,
    ]);

    $this->regularUser = User::factory()->create([
        'name' => 'Regular Test User',
        'is_popular_notified' => false,
    ]);
});

test('cron job identifies popular users and sends email once', function () {
    $likerIds = $this->likers->take(51)->pluck('id')->all();
    foreach ($likerIds as $actorId) {
        Swipe::create([
            'actor_user_id' => $actorId,
            'target_user_id' => $this->popularUser->id,
            'type' => 'like',
        ]);
    }


    $likerIds = $this->likers->take(5)->pluck('id')->all();
    foreach ($likerIds as $actorId) {
        Swipe::create([
            'actor_user_id' => $actorId,
            'target_user_id' => $this->regularUser->id,
            'type' => 'like',
        ]);
    }

    Artisan::call('people:notify-popular');

    Mail::assertSent(PopularUserMail::class, function ($mail) {
        return $mail->user->id === $this->popularUser->id;
    });

    Mail::assertSent(PopularUserMail::class, 1);

    $this->popularUser->refresh();
    expect($this->popularUser->is_popular_notified)->toBeTrue();

    $this->regularUser->refresh();
    expect($this->regularUser->is_popular_notified)->toBeFalse();


    Artisan::call('people:notify-popular');
    Mail::assertSent(PopularUserMail::class, 1);
});


test('command is registered in kernel and runs hourly', function () {
    $schedule = app(Schedule::class);

    $events = collect($schedule->events())->filter(function ($event) {
        return str_contains($event->command, 'people:notify-popular');
    });
    expect($events)->not->toBeEmpty('The people:notify-popular command is not registered in Kernel.php.');

    $event = $events->first();
    expect($event->expression)->toBe('0 * * * *', 'The command is not scheduled to run hourly.');
});

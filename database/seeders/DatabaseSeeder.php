<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Swipe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(100)->hasPictures(3)->create();

        $usersToLike = $users->random(3);
        foreach ($usersToLike as $targetUser) {
            Swipe::create([
                'actor_user_id' => 1,
                'target_user_id' => $targetUser->id,
                'type' => 'like',
            ]);
        }

        $usersToDislike = $users->whereNotIn('id', $usersToLike->pluck('id'))->random(1);
        foreach ($usersToDislike as $targetUser) {
            Swipe::create([
                'actor_user_id' => 1,
                'target_user_id' => $targetUser->id,
                'type' => 'dislike',
            ]);
        }

        $popularTargetUserId = 2;
        $likers = $users->where('id', '!=', $popularTargetUserId)
            ->where('id', '!=', 1)
            ->random(51);
        foreach ($likers as $liker) {
            Swipe::create([
                'actor_user_id' => $liker->id,
                'target_user_id' => $popularTargetUserId,
                'type' => 'like',
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Mail\PopularUserMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyPopularPeopleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:notify-popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify admin when someone has been liked by more than 50 users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting popularity check...');

        $threshold = 50;

        $popularUsers = User::query()
            ->withCount(['receivedSwipes' => function ($query) {
                $query->where('type', 'like');
            }])
            ->where('received_swipes_count', '>=', $threshold)
            ->where('is_popular_notified', false)
            ->get();

        if ($popularUsers->isEmpty()) {
            $this->info('No new popular users found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . $popularUsers->count() . ' new popular users.');

        $adminAddress = config('mail.admin_address', 'anzar.syahid@gmail.com');

        foreach ($popularUsers as $user) {
            Mail::to($adminAddress)->send(new PopularUserMail($user));

            $user->is_popular_notified = true;
            $user->save();

            $this->info('Alert sent for user: ID ' . $user->id . ', Name: ' . $user->name);
        }
    }
}

<?php

declare(strict_types=1);

use App\Console\Commands\NotifyPopularPeopleCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(NotifyPopularPeopleCommand::class)->hourly();

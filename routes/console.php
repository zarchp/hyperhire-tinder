<?php

use App\Console\Commands\NotifyPopularPeopleCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(NotifyPopularPeopleCommand::class)->hourly();

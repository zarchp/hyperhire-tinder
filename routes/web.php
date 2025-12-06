<?php

use App\Mail\PopularUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/* Route::get('/', function () {
    return new PopularUserMail(User::query()
        ->where('id', 1)
        ->withCount(['receivedSwipes' => function ($query) {
            $query->where('type', 'like');
        }])
        // ->having('received_swipes_count', '>=', 5)
        ->first());
}); */

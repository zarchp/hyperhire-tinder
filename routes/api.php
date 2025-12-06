<?php

use App\Http\Controllers\Api\People\LikedController;
use App\Http\Controllers\Api\People\RecommendationController;
use App\Http\Controllers\Api\People\SwipeController;
use App\Http\Controllers\Api\People\UndoSwipeController;
use App\Http\Middleware\SetTestUser;
use Illuminate\Support\Facades\Route;

Route::prefix('/people')
    ->middleware(SetTestUser::class)
    ->group(function () {
        Route::get('/', RecommendationController::class);
        Route::get('/liked', LikedController::class);
        Route::post('/swipes', SwipeController::class);
        Route::delete('/undo-swipes', UndoSwipeController::class);
    });

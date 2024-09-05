<?php

use App\Http\Controllers\LivechatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// routes/api.php
Route::get('/murid/livechat/{id_livechat}/messages', [LivechatController::class, 'fetchMessages']);

Route::get('/umum/livechat/{id_livechat}/messages', [LivechatController::class, 'UmumfetchMessages']);

Route::get('/ustaz/livechat/{id_livechat}/messages', [LivechatController::class, 'fetchMessagesUstaz']);

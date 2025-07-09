<?php

use App\Http\Controllers\SenhaTicketController;
use App\Http\Controllers\UserController;
use App\Models\SenhaTicket;
use Illuminate\Support\Facades\Route;

Route::post('user/create',[UserController::class, 'store'])->name('create_user');
Route::post('/login',[UserController::class,'login'])->name('login');
Route::get('senhaTicket',[SenhaTicketController::class,'show'])->name('senhaTicket');

Route::middleware('auth:sanctum')->group(function(){
    Route::post('senhaTicket/create',[SenhaTicketController::class, 'store'])->name('create_senhaTicket');
    Route::post('senhaTicket/call',[SenhaTicketController::class,'call'])->name('call_senha');
});
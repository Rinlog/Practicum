<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Applications;
use App\Livewire\Logs;
use App\Livewire\Settings;

Route::get('/', Login::class);
Route::get('/home', Home::class);
Route::get('/applications', Applications::class);
Route::get('/dashboard', Dashboard::class);
Route::get('/logs', Logs::class);
Route::get('/settings/{page}',Settings::class);
Route::Post("/logout",function(){
    session_start();
    session_destroy();
    return redirect("/");
});
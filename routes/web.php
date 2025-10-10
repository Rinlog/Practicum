<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Applications;
use App\Livewire\Logs;
use App\Livewire\Readings;
use App\Livewire\Settings;
use App\Livewire\UserSettings;
use App\Http\Controllers\Precache;
Route::get('/', Login::class);
Route::get('/home', Home::class);
Route::get('/applications', Applications::class);
Route::get('/dashboard', Dashboard::class);
Route::get("/readings",Readings::class);
Route::get('/logs', Logs::class);
Route::get('/settings/{page}',Settings::class);
Route::get("/profilesettings",UserSettings::class);
Route::Post("/logout",function(){
    session_start();
    session_destroy();
    session()->flush();
    return redirect("/");
});
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['can:view groups'])->get('/roles', function() {
        return view('roles');
    });
});

Route::get('/seed', function(){
    \Artisan::call('migrate:fresh');
    \Artisan::call('db:seed');
});

Route::get('/', function () {
    return view('groups');
})->name('groups');

Route::get('/users', function(){
    return view('users');
});

Route::get('/creategroup', function(){
    return view('create-group');
});

Route::get('/group/{slug}', function($slug){
    return view('group', compact('slug'));
})->name('group');

Route::get('/group/{slug}/createthread', function($slug){
    return view('create-thread', compact('slug'));
})->name('create-thread');

Route::get('/group/{slug}/thread/{thread}', function($slug, $thread){
    return view('thread', compact('slug', 'thread'));
})->name('thread');

Route::get('/user/profile', function(){
    return view('user');
})->name('user');

Route::get('/user/profile', function(){
    return view('user');
})->name('profile.show');

Route::get('/group/{slug}/requests', function($slug){
    return view('group-join-request', compact('slug'));
})->name('request');

Route::get('/group/{slug}/adduser', function($slug){
    return view('user-manage-group', compact('slug'));
})->name('user-manage-group');

Route::get('/group/{slug}/adduser/requests', function($slug){
    return view('group-manage-request', compact('slug'));
})->name('group-manage-request');

Route::get('/user/profile/{slug}', function($slug){
    return view('user-profile', compact('slug'));
})->name('user-profile');

Route::get('/user/create', function(){
    return view('create-profile');
})->name('create-profile');

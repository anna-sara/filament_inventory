<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Filament\Pages\Reserve;


Route::group(['domain' => 'boka.vbytes.se'], function(){
    Route::get('/', Reserve::class);
});


require __DIR__.'/auth.php';

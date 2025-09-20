<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Filament\Pages\Reserve;
use App\Models\Reserveditem;


//Route::group(['domain' => 'boka.vbytes.se'], function(){
    Route::get('/boka', Reserve::class);
//});

//Route::get('/demo', function () { return new App\Mail\Delivered(Reserveditem::first()); });


require __DIR__.'/auth.php';

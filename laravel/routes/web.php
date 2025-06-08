<?php

use App\Http\Controllers\UserImports\UserImportsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=> 'rest/user-import'], function () {

    Route::get('group-by-date', [UserImportsController::class, 'groupByDate']);

});

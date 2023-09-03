<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str; // import library Str
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Laravel\Lumen\Http\Request as HttpRequest;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RacesController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\PDFController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('races')->group(function () {
        Route::get('/', [RacesController::class, 'getAllRaces']);
        Route::post('/create', [RacesController::class, 'createRaces']);
        Route::get('/{id}', [RacesController::class, 'getRaceById']);
        Route::get('/user/races', [RacesController::class, 'getUserRaces']);
        Route::get('/user/races/{id}', [RacesController::class, 'getUserRaceById']);
        Route::get('/user/status', [RacesController::class, 'cekStatusUserRaces']);
        Route::post('/user/joinrace', [RacesController::class, 'joinRace']);
        Route::get('/user/progres', [RacesController::class, 'getProgressUserRaces']);
        Route::get('/user/generate-pdf/{id}', [PDFController::class, 'generatePDF']);
        
    });
    
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivitiesController::class, 'getAllUserActivities']);
        Route::post('/create', [ActivitiesController::class, 'createActivities']);
        Route::post('/delete/{id}', [ActivitiesController::class, 'delUserActivities']);
    });
});





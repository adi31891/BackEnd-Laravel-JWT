<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Me\ProfileController;
use App\Http\Controllers\Me\ArticleController as MeArticleController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/sign-up', [AuthController::class, 'signUp']);
Route::post('/sign-in', [AuthController::class, 'signIn']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{categorySlug}', [CategoryController::class, 'show']);


Route::middleware('auth:api')->group(function() {
    Route::prefix('/me')->group(function(){
        Route::get('/profile', [ProfileController::class, 'show']);

        //untuk update data pake put
        Route::put('/profile', [ProfileController::class, 'update']);

        Route::apiResource('articles', MeArticleController::class);

    });



    Route::post('/sign-out', [AuthController::class, 'signOut']);
});

Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

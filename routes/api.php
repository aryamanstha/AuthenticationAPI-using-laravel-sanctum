<?php

use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Models\User;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//-------------------Public Routes---------------------
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/getresetemail',[PasswordResetController::class,'getResetEmail']);
Route::post('/reset-password/{token}',[PasswordResetController::class,'reset']);

//--------------------Protected Routes-----------------
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/logout',[UserController::class,'logout']);
    Route::get('/loggedin',[UserController::class,'getLoggedUser']);
    Route::post('/changepassword',[UserController::class,'changePassword']);
});

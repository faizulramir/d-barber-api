<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\SavedToken;
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

Route::post('/booking/post', [App\Http\Controllers\BookingController::class, 'bookingPost'])->name('bookingPost');
Route::get('/booking/get/{phone}', [App\Http\Controllers\BookingController::class, 'bookingGet'])->name('bookingGet');
Route::get('/times/get/{date}', [App\Http\Controllers\UtilsController::class, 'timesGet'])->name('timesGet');
Route::get('/shop/get', [App\Http\Controllers\UtilsController::class, 'shopGet'])->name('shopGet');

Route::post('/tokens/create', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response([
            'status' => 'Error',
            'msg' => 'Invalid Credentials'
        ], 401);
    }

    $saved_token = SavedToken::first();

    if (!$saved_token) {
        if ($user->tokens()) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $saveToken = new SavedToken;
        $saveToken->user_id = $user->id;
        $saveToken->token = $token;
        $saveToken->save();
    } else {
        $token = $saved_token->token;
    }

    $response = [
        'status' => "OK",
        'msg' => 'Authenticated',
        'user' => $user,
        'token' => $token,
    ];
    
    return response($response, 201);
});
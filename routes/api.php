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

Route::middleware('auth:sanctum')->group(function(){
    // Route::get('get_notification_list', [App\Http\Controllers\ApiController::class, 'get_notification_list'])->name('get_notification_list');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

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
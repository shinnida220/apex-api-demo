<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/ping', [TransactionsController::class, 'index']);
Route::get('/create-account', [TransactionsController::class, 'createAccount']);
Route::get('/accounts', [TransactionsController::class, 'listAccounts']);
Route::post('/validate-transaction', [TransactionsController::class, 'validateTransaction']);
Route::post('/complete-transaction', [TransactionsController::class, 'completeTransaction']);
Route::get('/verify-transaction/{transactionRef}', [TransactionsController::class, 'verifyTransaction']);
Route::get('/transactions', [TransactionsController::class, 'listTransactions']);

Route::fallback(function(){
    return response()->json([
        'status' => false,
        'message' => 'Invalid api endpoint.'
    ], 404);
});

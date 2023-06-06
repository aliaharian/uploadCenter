<?php

use App\Http\Controllers\UploadChunkController;
use App\Http\Controllers\uploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/fileMeta/insert', [UploadChunkController::class, "insertFileMeta"]);
Route::post('/filePart/insert', [UploadChunkController::class, "insertFilePart"]);

Route::post('/fileMeta/view', [UploadChunkController::class, "viewFileMeta"]);
Route::post('/filePart/view', [UploadChunkController::class, "viewFilePart"]);

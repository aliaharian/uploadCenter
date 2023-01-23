<?php

use App\Http\Controllers\uploadController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
//list
Route::get('/', [uploadController::class, 'list'])->name('list');
//delete file with delete method
Route::delete('/delete/{id}', [uploadController::class, 'delete'])->name('delete');

//upload get and post route using upload controller


Route::get('/upload', [uploadController::class, 'index']);

Route::post('/upload', [uploadController::class, 'store'])->name('upload');

//retrive file with hash

Route::get('/file/{hash}', [uploadController::class, "view"])->name('file');
<?php

use App\Http\Controllers\TaskController;
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


Route::get('/',[TaskController::class,'taskform']);
Route::post('/addTask', [TaskController::class, 'addTask']);
Route::get('/getAllTasks', [TaskController::class, 'getAllTasks']);
Route::post('/deleteTask', [TaskController::class, 'deleteTask']);
Route::post('/markAsComplete', [TaskController::class, 'markAsComplete']);
<?php

use App\Http\Controllers\PositionController;
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

Route::get('/', function () {
    return redirect('/positions');
})->name('index');


Illuminate\Support\Facades\Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function (){return redirect('/positions');})->name('home');

Route::group(['namespace'=>'App\Http\Controllers\Position'], function(){
    Route::get('/positions', 'IndexController')->name('positions.index');
    Route::get('/position/create', 'CreateController')->name('position.create');
    Route::post('/position/store', 'StoreController')->name('position.store');
    Route::get('/position/{position}/edit', 'EditController')->name('position.edit');
    Route::put('/position/{position}', 'UpdateController')->name('position.update');
    Route::get('/position/{position}/delete', 'DeleteController')->name('position.delete');
    Route::get('/position/{position}/destroy', 'DestroyController')->name('position.destroy');
    Route::post('/position/find', 'FindController')->name('position.find');
    Route::get('/position/{position}/moveemployees','MoveemployeesController')->name('position.moveemployees');
    Route::post('position/{position}/moveemployees','MoveemployeesController')->name('position.moveemployees');
});

Route::group(['namespace'=>'App\Http\Controllers\Employee'], function(){
    Route::get('/employees', 'IndexController')->name('employees.index');
    Route::get('/employee/create', 'CreateController')->name('employee.create');
    Route::post('/employee/store', 'StoreController')->name('employee.store');
    Route::get('/employee/{employee}/edit', 'EditController')->name('employee.edit');
    Route::put('/employee/{employee}', 'UpdateController')->name('employee.update');
    Route::get('/employee/{employee}/delete', 'DeleteController')->name('employee.delete');
    Route::get('/employee/{employee}/destroy', 'DestroyController')->name('employee.destroy');
    Route::post('/employee/find', 'FindController')->name('employee.find');
    Route::post('/employees/move/{employee}', 'MoveController')->name('employees.move');
});



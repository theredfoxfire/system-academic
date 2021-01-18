<?php

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

Route::get('/PDF/byClass/{class_id}/{subject_id}/{academic_year_id}', 'PDFController@byClass');
Route::get('/PDF/byStudent/{student_id}', 'PDFController@byStudent');
Route::get('/PDF/byClassBulk/{class_id}', 'PDFController@byClassBulk');

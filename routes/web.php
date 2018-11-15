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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::get('/test1', 'APIController@createNewTeacher');
Route::get('/test2', 'APIController@createNewStudent');
Route::get('/test3', 'APIController@createNewSchoolClass');
Route::get('/api/lessons/get/{year?}/{week?}', 'APIController@getLessons');
Route::post('/api/lessons/save', 'APIController@saveLesson');
Route::get('/api/lessons/dates/{classId}', 'APIController@getLessonDates');
Route::get('/api/students/get/{classId}', 'APIController@getStudents');
Route::get('/api/grades/get/{classId}', 'APIController@getClassLessonGrades');
Route::post('/api/grades/save', 'APIController@saveGrades');
Route::get('/api/classes/get', 'APIController@getClasses');
Route::get('/api/subjects/get', 'APIController@getSubjects');

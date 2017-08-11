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

Route::post('login', 'LoginController@login');
Route::get('log_out', 'LoginController@log_out');

Route::post('register', 'UserController@register');
Route::get('activate', 'UserController@activate');

Route::group(['middleware' => ['login_auth']], function () {

    Route::get('user', 'UserController@user');
    Route::get('user/{uid}', 'UserController@user');


    Route::get('media', 'MediaController@media');
    Route::get('media/{mid}', 'MediaController@media');
    Route::post('media', 'MediaController@create_media');
    Route::put('media', 'MediaController@update_media');
    Route::delete('media', 'MediaController@delete_media');


    Route::get('page_group', 'PageGroupController@get_page_group');
    Route::get('page_group/{pid}', 'PageGroupController@get_page_group');
    Route::post('page_group', 'PageGroupController@create_page_group');
    Route::put('page_group', 'PageGroupController@create_page_group');
    Route::delete('page_group', 'PageGroupController@delete_page_group');
});

//Route::get('page_group', 'PageGroupController@create_page_group');

Route::get('fpdf', 'PdfController@generate_pdf');

Route::post('book', 'BookController@create_book');
Route::put('book', 'BookController@create_book');

Route::get('page', 'PageController@page_list');
Route::get('section', 'SectionController@section_list');
Route::get('question', 'QuestionsController@question_list');
Route::get('question/{question_id}', 'QuestionsController@question_list');

Route::post('subject', 'SubjectController@create_subject');
Route::put('subject', 'SubjectController@create_subject');

Route::post('question_type', 'QuestionTypeController@create_question_type');
Route::put('question_type/{_id}', 'QuestionTypeController@update_question_type');




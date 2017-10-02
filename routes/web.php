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


    Route::get('question_type', 'QuestionTypeController@question_type');
    Route::get('question_type/{_id}', 'QuestionTypeController@question_type');
    Route::post('question_type', 'QuestionTypeController@create_question_type');
    Route::put('question_type', 'QuestionTypeController@update_question_type');
    Route::delete('question_type', 'QuestionTypeController@delete_question_type');

    Route::get('organization', 'OrganizationController@organization');
    Route::get('organization/{_id}', 'OrganizationController@organization');
    Route::post('organization', 'OrganizationController@create_organization');
    Route::put('organization', 'OrganizationController@create_organization');
    Route::delete('organization', 'OrganizationController@delete_organization');

    Route::get('book', 'BookController@book_list');
    Route::get('book/{_id}', 'BookController@book_list');
    Route::post('book', 'BookController@create_book');
    Route::put('book', 'BookController@create_book');
    Route::delete('book', 'BookController@delete_book');

    Route::get('page', 'PageController@page_list');
    Route::get('page/{page_id}', 'PageController@page_list');
    Route::post('page', 'PageController@add_or_update_page');
    Route::put('page', 'PageController@add_or_update_page');
    Route::delete('page', 'PageController@delete_page');

    Route::get('section', 'SectionController@section_list');
    Route::get('section/{_id}', 'SectionController@section_list');
    Route::get('section_search', 'SectionController@section_search');
    Route::post('section', 'SectionController@add_or_update_section');
    Route::put('section', 'SectionController@add_or_update_section');
    Route::delete('section', 'SectionController@delete_section');

    Route::get('question', 'QuestionsController@question_list');
    Route::get('question/{_id}', 'QuestionsController@question_list');
    Route::get('question_search', 'QuestionsController@question_search');
    Route::post('question', 'QuestionsController@add_or_update_question');
    Route::put('question', 'QuestionsController@add_or_update_question');
    Route::delete('question', 'QuestionsController@delete_question');

    Route::get('subject', 'SubjectController@subject_list');
    Route::get('subject/{_id}', 'SubjectController@subject_list');
    Route::post('subject', 'SubjectController@create_subject');
    Route::put('subject', 'SubjectController@create_subject');
    Route::delete('subject', 'SubjectController@delete_subject');
    
    Route::get('layout', 'LayoutController@layout_list');
    Route::get('layout/{_id}', 'LayoutController@layout_list');
    Route::post('layout', 'LayoutController@add_or_update_layout');
    Route::put('layout', 'LayoutController@add_or_update_layout');
    Route::delete('layout', 'LayoutController@delete_layout');
    
    Route::get('resource', 'ResourceController@resource');
    Route::get('resource/{_id}', 'ResourceController@resource');
    Route::post('resource', 'ResourceController@create_or_update_resource');
    Route::put('resource', 'ResourceController@create_or_update_resource');
    Route::delete('resource', 'ResourceController@delete_resource');
    
    Route::get('class', 'ClassController@class_list');
    Route::get('class/{_id}', 'ClassController@class_list');
    Route::post('class', 'ClassController@add_or_update_class');
    Route::put('class', 'ClassController@add_or_update_class');
    Route::delete('class', 'ClassController@delete_class');
    
    Route::get('class_flow', 'ClassFlowController@class_flow_list');
    Route::get('class_flow/{_id}', 'ClassFlowController@class_flow_list');
    Route::post('class_flow', 'ClassFlowController@add_or_update_class_flow');
    Route::put('class_flow', 'ClassFlowController@add_or_update_class_flow');
    Route::delete('class_flow', 'ClassFlowController@delete_class_flow');
   
    Route::get('keyword','KeywordController@keyword_list');
    Route::get('keyword/{_id}','KeywordController@keyword_list');
    Route::post('keyword','KeywordController@create_or_update_keyword');
    Route::put('keyword','KeywordController@create_or_update_keyword');
    Route::delete('keyword','KeywordController@delete_keyword');
    
    Route::get('domain', 'DomainController@domain_list');
    Route::get('domain/{_id}', 'DomainController@domain_list');
    Route::post('domain', 'DomainController@add_or_update_domain');
    Route::put('domain', 'DomainController@add_or_update_domain');
    Route::delete('domain', 'DomainController@delete_domain');
    
    Route::get('sub_domain', 'SubDomainController@sub_domain_list');
    Route::get('sub_domain/{_id}', 'SubDomainController@sub_domain_list');
    Route::post('sub_domain', 'SubDomainController@add_or_update_sub_domain');
    Route::put('sub_domain', 'SubDomainController@add_or_update_sub_domain');
    Route::delete('sub_domain', 'SubDomainController@delete_sub_domain');

});

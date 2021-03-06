<?php

use Illuminate\Http\Request;
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


//TEST Route - Signup route only for test.
Route::group(['middleware' => ['cors']], function () {
    Route::post('login', 'AuthController@logIn');

    Route::group(['prefix' => 'password'], function () {
        Route::post('forgot', 'PasswordResetController@forgotPasswordRequest');
        Route::get('verify/{token}', 'PasswordResetController@verifyToken');
        Route::post('reset', 'PasswordResetController@resetPassword');
    });

    /*
    * Job Routes for Enclave Recruitment web.
    */
    /*Route::get("job","JobController@index");
    Route::get("job-web/{id}","JobController@showJobForCandidatesPage");*/

    /*
    * Article routes for Enclave Recruitment web.
    */
    Route::post("article-web","ArticleController@showListArticleForCandidatePage");
    Route::get("article-web/{id}","ArticleController@showArticleForCandidatePage");
    Route::post("article-related/{id}","ArticleController@showArticleRelatedForCandidatePage");

    /*
    * Store candidate's information .
    */
    Route::post("candidate","CandidateController@store");

    Route::group(['middleware' => 'auth:api'], function() {
        /*
        * Auth routes
        */
        Route::put('change-password','AuthController@changePassword');
        Route::get('logout', 'AuthController@logout');        //Must login and use access_token to access these route.

        /*
        * Profile routes
        */
        Route::get('current-profile','UserController@showCurrentInfoUser'); //Show current profile's information
        Route::put('profile','UserController@updateCurrentProfile'); //Update profile's information
        Route::post('profile/avatar',"UserController@changeAvatar");

        /*
        * Role routes
        */
        Route::post('list-role','RoleController@index')->middleware('can:Role.list');
        Route::get('role/{id}','RoleController@show')->middleware('can:Role.list');
        Route::post('role','RoleController@store')->middleware('can:Role.create');
        Route::put('role/{id}','RoleController@update')->middleware('can:Role.edit');
        Route::delete('role','RoleController@destroy')->middleware('can:Role.delete');
        Route::get('role/{id}/edit','RoleController@edit')->middleware('can:Role.edit');

        /*
        * Permission routes
        */
        Route::post('permission','PermissionController@index');

        /*
        * User routes
        */
        Route::post('list-user','UserController@index')->middleware('can:user.view');
        Route::post('user','UserController@store')->middleware('can:user.create');
        Route::get('user/{id}','UserController@show')->middleware('can:user.view');
        Route::put('user/{id}','UserController@update')->middleware('can:user.edit');
        Route::delete("user","UserController@destroy")->middleware("can:user.delete");

        /*
        * Notifications route
        */
        Route::get('notifications', 'NotificationController@notifications');
        Route::get('notifications/{id}', 'NotificationController@show');

        /*
        * Job routes
        */
        Route::post("list-job","JobController@index")->middleware("can:job.view");
        Route::get("job/{id}","JobController@show")->middleware("can:job.view");
        Route::post("job","JobController@store")->middleware("can:job.create");
        Route::put("job/{id}","JobController@update")->middleware("can:job.edit");
        Route::delete("job","JobController@destroy")->middleware("can:job.delete");

        /*
        * Article routes
        */
        Route::post("list-article","ArticleController@index")->middleware("can:article.view");
        Route::get("article/{id}","ArticleController@show")->middleware("can:article.view");
        Route::post("article","ArticleController@store")->middleware("can:article.create");
        Route::post("article/{article}","ArticleController@update")->middleware("can:article.edit,article");
        Route::delete("article","ArticleController@destroy")->middleware("can:article.delete");
        Route::get("publish-article/{id}","ArticleController@publish")->middleware("can:article-publish.edit");

        /*
        * Interviewer routes
        */
        Route::post("list-interviewer","InterviewerController@index")->middleware("can:interviewer.view");
        Route::get("interviewer/{id}","InterviewerController@show")->middleware("can:interviewer.view");
        Route::post("interviewer","InterviewerController@store")->middleware("can:interviewer.create");
        Route::put("interviewer/{id}","InterviewerController@update")->middleware("can:interviewer.edit");
        Route::post("interviewer-avatar","InterviewerController@updateNewAvatar")->middleware("can:interviewer.edit");
        Route::delete("interviewer","InterviewerController@destroy")->middleware("can:interviewer.delete");

        /*
        * Candidate routes
        */
        Route::post("list-candidate","CandidateController@index")->middleware("can:candidate.view");
        Route::get("candidate/{id}","CandidateController@show")->middleware("can:candidate.view");
        Route::post("candidate-status","CandidateController@updateStatus")->middleware("can:candidate.edit");
        Route::delete("candidate","CandidateController@destroy")->middleware("can:candidate.delete");

         /*
         * Interview routes
         */
        Route::post("list-interview","InterviewController@index")->middleware("can:interview.view");
        Route::post("interview","InterviewController@store")->middleware("can:interview.create");
        Route::get("interview/{id}","InterviewController@show")->middleware("can:interview.view");
        Route::delete("interview","InterviewController@destroy")->middleware("can:interview.delete");
        Route::put("interview/{id}","InterviewController@update")->middleware("can:interview.edit");


        /*
         * Category routes
         */
        Route::post("category","CategoryController@index");

        /*
         * Format article routes
         */
        Route::post("format-article","FormatArticleController@store")->middleware("can:format.management");
        Route::put("format-article/{id}","FormatArticleController@update")->middleware("can:format.management");
        Route::delete("format-article","FormatArticleController@destroy")->middleware("can:format.management");
        Route::get("format-article/{id}","FormatArticleController@show");
        Route::get("format-article","FormatArticleController@index");


        /*
         * Sessions for front-end's authorize
         */

        Route::group(['prefix' => 'session'], function ()
        {
            Route::get('user-information', 'SessionController@informationUser');
        });
    });
});





<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/login',function (){
    return view('auth.login');
});
Route::post('/login', 'Auth\AuthController@postLogin');
Route::post('register', 'Auth\AuthController@postRegister'); //РАСКОМЕНТИТЬ ПРИ НЕОБХОДИМОСТИ!
Route::get('register', 'Auth\AuthController@getRegister');   //РАСКОМЕНТИТЬ ПРИ НЕОБХОДИМОСТИ!

////ВСЕ РОУТЫ  ПИШИ В ГРУППУ НИЖЕ. LOGIN ИСКЛЮЧЕНИЕ, ИНАЧЕ БУДЕТ БЕСКОНЕЧНЫЙ ЦИКЛ ПЕРЕАДРЕССАЦИИ


//В ЭТОЙ ГРУППЕ ВСЕ РОУТЫ ПРОХОДЯТ ПРОВЕРКУ СЕССИЙ, КУК И АВТОРИЗАЦИИ
Route::group(['middleware' => ['web']], function () {
    Route::get('/job', function () {
        $job = (new App\Jobs\SendReminderEmail())->delay(60 * 5);
        dispatch($job);
    });

    Route::get('logout', function(){
        Auth::logout();
        return view('auth.login');
    });
    Route::get('auth', 'Auth\AuthController@getLogout');

    Route::any('/','PageBuilderController@index');
    //Route::get('/login', 'Auth\AuthController@getLogin');

    Route::post('/doc', ['middleware' => 'showDocument', function()
    {
        return view('error_input');
    }]);

    Route::get('/doc/{number}/','PageBuilderController@docInfo');
    Route::post('/task','AjaxFormController@getFullTaskById');
    Route::post('/return','PageBuilderController@returnInfo');
    Route::post('/ajax/addProblemTask','AjaxFormController@addGoodsProblemTask');
    Route::post('/ajax/getfortask','AjaxFormController@getWithClientByID');
    Route::post('/ajax/getcdek','AjaxFormController@CDEK');
    Route::post('/ajax/bitrixlist','AjaxFormController@getBitrixList');
    Route::post('/ajax/pvzlist','AjaxFormController@getPVZList');
    Route::get('/pvzlist/{cityID}/','AjaxFormController@getPVZList');
    Route::post('/ajax/getforbitrix','AjaxFormController@getBitrix');
    Route::post('/ajax/delayTask','AjaxFormController@delayTask');
    Route::get('/ajax/getforbitrix','AjaxFormController@getBitrix');
    Route::get('/bishtrix','BitrixController@getNewO');
    Route::get('/docupd','DocumentController@import');
    Route::get('/addcdek','PageBuilderController@newCDEK');
    Route::post('/addcdek','PageBuilderController@newCDEK');
    Route::post('/ajax/addcdek','AjaxFormController@newCDEK');
    Route::post('/ajax/addcdekelem','AjaxFormController@newCDEKelem');
    Route::get('/settask','WelcomeController@setTask');
    Route::get('/rv','TaskController@reviewTasks');
    Route::post('/ajax/searchSteps','SearchStatsController@getSearchSteps');
    Route::post('/ajax/runSearchStep','SearchStatsController@runSearchStep');
    Route::get('/search','PageBuilderController@getSearchResult');
    Route::get('/ajax/searchStats','SearchStatsController@searchStats');
    Route::get('/searchman','PageBuilderController@searchManager');
    Route::post('/searchman','PageBuilderController@searchManager');
    Route::get('/checkcaptcha','SearchStatsController@yaRestore');
    Route::post('/checkcaptcha','SearchStatsController@yaRestore');
    Route::get('/rds','PageBuilderController@getRDSList');
    Route::get('/bitrix','PageBuilderController@getBitrixList');
    Route::post('/ajax/changeTaskStatus','AjaxFormController@changeTaskStatus');
    Route::post('/ajax/DoneTask','AjaxFormController@completeTask');
    Route::get('/print/{number}/print.pdf','CDEKController@orderPrint');
});


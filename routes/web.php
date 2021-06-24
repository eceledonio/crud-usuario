<?php


Route::get('/', function () {
    return View ('Welcome');
});

Route::get('/usuarios', 'UserController@index')->name('users.index');

Route::get('/usuarios/{user}','UserController@show')->where('user', '[0-9]+')->name('users.show');

Route::get('/usuarios/nuevo', 'UserController@create')->name('users.create');

Route::post('/usuarios', 'UserController@store');

Route::get('/usuarios/{user}/editar', 'UserController@edit')->name('users.edit');

Route::put('/usuarios/{user}','UserController@update');

Route::get('/saludos/{name}/{nickname?}', 'WelcomUserController@index');

Route::get('/usuarios/{id}/edit', 'UserController@edit')->where('id', '[0-9]+');

Route::delete('/usuarios/{user}','UserController@destroy')->name('users.destroy');








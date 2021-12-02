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


use App\Model\SystemSetting;

View::composer(['layouts.header','layouts.sidebar','auth.login'], function ($data) {
            $data['systemInformation'] = SystemSetting::where('sys_id',1)->first();
        });
Route::post('user-login','Auth\LoginController@login');
Auth::routes();

Route::get('/', 'Admin\DashboardController@index');
Route::get('/home', 'Admin\DashboardController@index');

Route::get('/users', 'Admin\UsersController@index');
Route::get('/update-users/{id}', 'Admin\UsersController@edit');
Route::get('/delete-users/{id}', 'Admin\UsersController@destroy');
Route::get('/family-member-delete/{id}', 'Admin\UsersController@memberDestroy');
Route::post('/update-users','Admin\UsersController@update');
Route::post('/user-update-status','Admin\UsersController@updateStatus'); // Change user status 0 = Active 1 = Inctive
Route::post('/user-details','Admin\UsersController@getUserdetails'); // Change user status 0 = Active 1 = Inctive
Route::get('/user-details-export-excel/{type}','Admin\UsersController@exportExcel');
Route::get('/user-details-export-pdf','Admin\UsersController@exportPdf');
Route::post('change-user-password','Admin\UsersController@updatepassword');
Route::post('change-admin-password','Admin\UsersController@updateAdminpassword');

Route::get('user-profile','Admin\UsersController@userprofile');
Route::post('update-users-profile','Admin\UsersController@updateprofile');

Route::get('/familys', 'Admin\FamilysController@index');
Route::get('/view-family-detail/{id}', 'Admin\FamilysController@viewDetails');
Route::get('/edit-family-detail/{id}', 'Admin\FamilysController@editDetails');
Route::get('/edit-family-member-detail/{id}', 'Admin\FamilysController@editFamilyMemberDetails');
Route::get('/delete-familys-members/{id}', 'Admin\FamilysController@destroy');
Route::post('/familys-update-status','Admin\FamilysController@updateStatus'); // Change user status 0 = Active 1 = Inctive

Route::get('/chores', 'Admin\ChorsController@index');
Route::get('/view-chores-list/{id}','Admin\ChorsController@viewChores');
Route::get('/view-finished-chores-list/{id}','Admin\ChorsController@viewFinishedChores');
Route::get('/view-daily-chores-list/{id}','Admin\ChorsController@viewDailyChores');
Route::get('/delete-child-chores/{id}', 'Admin\ChorsController@destroy');
Route::get('/message', 'Admin\MessageController@index');

// Setting Module
Route::get('/setting', 'Admin\SystemSettingController@index');
Route::post('/store-system-setting','Admin\SystemSettingController@store');

// ******************* BEGIN PRESET CHORES MODULE ***********************

Route::get('/preset-chores', 'Admin\PresetChoresController@index');
Route::post('/create-preset-chores', 'Admin\PresetChoresController@store');
Route::post('/update-preset-chores', 'Admin\PresetChoresController@update');
Route::get('/delete-preset-chores/{id}', 'Admin\PresetChoresController@destroy');
Route::post('/status-preset-chores','Admin\PresetChoresController@changestatus'); // Change user status 0 = Active 1 = Inctive

Route::get('/export-preset-chores/{type}','Admin\PresetChoresController@exportExcel'); // Excel file
Route::get('/export-pdf-preset-chores','Admin\PresetChoresController@exportPdf'); // Pdf file

Route::get('/chores-icons', 'Admin\PresetChoresController@choresIcons');
Route::post('/create-chores-icons','Admin\PresetChoresController@storechoresIcons');
Route::get('/delete-chores-icon/{id}','Admin\PresetChoresController@deletechoresIcons');

// ******************* END PRESET CHORES MODULE ***********************

// ******************* BEGIN CATEGORY REWARD MODULE ***********************

Route::get('/reward-category', 'Admin\CategoryRewardController@index');
Route::post('/create-reward-category', 'Admin\CategoryRewardController@store');
Route::post('/update-reward-category', 'Admin\CategoryRewardController@update');
Route::get('/delete-reward-category/{id}', 'Admin\CategoryRewardController@destroy');
Route::post('/status-reward-category','Admin\CategoryRewardController@changestatus'); // Change user status 0 = Active 1 = Inctive

Route::get('/export-reward-category/{type}','Admin\CategoryRewardController@exportExcel'); // Excel file
Route::get('/export-pdf-reward-category','Admin\CategoryRewardController@exportPdf'); // Pdf file

// ******************* END CATEGORY REWARD MODULE ***********************

// ******************* BEGIN BRAND REWARD MODULE ***********************

Route::get('/reward-brand', 'Admin\BrandsController@index');
Route::post('/create-reward-brand', 'Admin\BrandsController@store');
Route::post('/update-reward-brand', 'Admin\BrandsController@update');
Route::get('/delete-reward-brand/{id}', 'Admin\BrandsController@destroy');
Route::post('/status-reward-brand','Admin\BrandsController@changestatus'); // Change user status 0 = Active 1 = Inctive

Route::get('/reward-sub-brand/{id}','Admin\BrandsController@subbrandlist');
Route::post('/create-reward-sub-brand','Admin\BrandsController@storesubBrand');
Route::get('/delete-reward-sub-brand/{id}','Admin\BrandsController@deleteBrandIcon');

Route::get('/export-reward-brand/{type}','Admin\BrandsController@exportExcel'); // Excel file
Route::get('/export-pdf-reward-brand','Admin\BrandsController@exportPdf'); // Pdf file

// ******************* END BRAND REWARD MODULE ***********************

// ******************* BEGIN REWARD NAME REWARD MODULE ***********************

Route::get('/reward-name', 'Admin\RewardNameController@index');
Route::post('/create-reward-name', 'Admin\RewardNameController@store');
Route::post('/update-reward-name', 'Admin\RewardNameController@update');
Route::get('/delete-reward-name/{id}', 'Admin\RewardNameController@destroy');
Route::post('/status-reward-name','Admin\RewardNameController@changestatus'); // Change user status 0 = Active 1 = Inctive

Route::get('/export-reward-name/{type}','Admin\RewardNameController@exportExcel'); // Excel file
Route::get('/export-pdf-reward-name','Admin\RewardNameController@exportPdf'); // Pdf file

// ******************* END REWARD NAME REWARD MODULE ***********************

// Product Module

Route::get('/products', 'Admin\ProductsController@index');
Route::post('/create-products', 'Admin\ProductsController@store');
Route::post('/update-products', 'Admin\ProductsController@update');
Route::post('/sub-category-brands', 'Admin\ProductsController@subCategoryBrands');
Route::get('/delete-products/{id}', 'Admin\ProductsController@destroy');

Route::get('/reward-list', 'Admin\RewardsController@index');
Route::get('/view-reward-list/{id}','Admin\RewardsController@viewreward');
Route::get('/view-expired-reward-list/{id}','Admin\RewardsController@expiredreward');
Route::get('/delete-child-reward/{id}', 'Admin\RewardsController@destroy');

Route::get('/claims', 'Admin\ClaimsController@index');
Route::get('/view-claims/{id}','Admin\ClaimsController@viewreward');
Route::get('/delete-child-reward/{id}', 'Admin\ClaimsController@destroy');

Route::get('/help', 'Admin\HelpController@index');
Route::post('/help-update-status','Admin\HelpController@updateStatus'); // Change user status 0 = Active 1 = Inctive
Route::get('/delete-help/{id}', 'Admin\HelpController@destroy');

Route::get('/privacy', 'Admin\PrivacyController@privacy');
Route::get('/contact-us', 'Admin\PrivacyController@contact');






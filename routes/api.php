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

Route::post('login','RestApi\AuthController@signIn');
Route::post('signup','RestApi\AuthController@signUp');

// EDIT PROFILE
Route::post('edit_profile', 'RestApi\UserController@profileEdit');
Route::post('edit_profile_image' , 'RestApi\UserController@editProfileImage');
Route::post('user-details','RestApi\UserController@getUserdetails'); // Change user status 0 = Active 1 = Inctive

Route::post('add_family_member', 'RestApi\UserController@addfamilyMember');
Route::group([
      'middleware' => 'api.token'
    ], function() {
		Route::post('family_member_list', 'RestApi\UserController@familymemberList');
		Route::post('family_child_list', 'RestApi\UserController@childList');

		// ****************************** BEGIN NOTIFICATION MODULE **************************************

		Route::post('notification-list', 'RestApi\NotificationController@notificationList');
		Route::post('unread-notification-count', 'RestApi\NotificationController@unreadNotificationCount');

		// ****************************** BEGIN NOTIFICATION MODULE **************************************
});

Route::post('change_password' , 'RestApi\AuthController@updatePassword');
Route::post('forget_password' , 'RestApi\AuthController@forgetPassword');
Route::post('update_details', 'RestApi\UserController@updateDetails');
Route::post('admin_update_child_profile','RestApi\UserController@childProfileUpdate');

Route::post('contact-admin', 'RestApi\UserController@contactAdmin');

Route::get('chore-icon-list','RestApi\ChoresController@getChoreIcons');

Route::post('admin_assigned_chores_list','RestApi\ChoresController@assignedChores');
Route::post('admin_finished_chores_list','RestApi\ChoresController@finishedChores');
Route::post('assigned_chores_filter','RestApi\ChoresController@assignedChoresFilter');
Route::post('finished_chores_filter','RestApi\ChoresController@finishedChoresFilter');
Route::get('preset-chores-list','RestApi\ChoresController@presetChores');

Route::post('new-chores','RestApi\ChoresModuleController@createChore'); // Admin and parents create new chores
Route::post('delete-chores','RestApi\ChoresModuleController@deleteChore'); // Delete Chores
Route::post('new-chores-child','RestApi\ChoresModuleController@addChildChores');
Route::post('chores_id_wise_details','RestApi\ChoresModuleController@editChores');
Route::post('update_chores','RestApi\ChoresModuleController@updateChores');
Route::post('chores_is_complete','RestApi\ChoresModuleController@iscompleteChores');
Route::post('approve-chores','RestApi\ChoresModuleController@approvedChores');

// ****************************** BEGIN REWARD MODULE **************************************

Route::get('reward-category-list','RestApi\RewardsController@rewardCategoryList');
Route::post('reward-brands-list','RestApi\RewardsController@brandList');
Route::post('reward-list','RestApi\RewardsController@rewardList'); // Rewards list
Route::post('filter-reward-list','RestApi\RewardsController@filterRewards'); // Create Rewards
Route::post('user-wise-reward-list','RestApi\RewardsController@userWiseRewardList'); // Create Rewards
Route::post('user-wise-filter-reward-list','RestApi\RewardsController@userWisefilterRewards'); // Create Rewards
Route::post('create-reward','RestApi\RewardsController@createReward'); // Create Rewards
Route::post('delete-reward','RestApi\RewardsController@deleteReward'); // Create Rewards

Route::post('reward-is-conformation','RestApi\RewardsController@isConformation');

Route::post('reward-details','RestApi\RewardsController@rewardDetails');
Route::post('update-reward','RestApi\RewardsController@updateReward');

Route::post('create-claim','RestApi\RewardsController@createClaim');
Route::post('claim-list','RestApi\RewardsController@claimList'); // Approved Rewards List
Route::post('filter-claim-list','RestApi\RewardsController@filterclaimList'); // Approved Rewards List
Route::post('user-wise-claim-list','RestApi\RewardsController@childClaimList'); // Child Claim List
Route::post('filter-user-wise-claim-list','RestApi\RewardsController@fieldchildClaimList'); // Child Wise Claim List

// ****************************** END REWARD MODULE **************************************

// ****************************** BEGIN CHAT MODULE **************************************
Route::post('message-family-member-list', 'RestApi\MessageController@familymemberList');
Route::post('send-message', 'RestApi\MessageController@createMessage');
Route::post('member-wise-message-list', 'RestApi\MessageController@messageList');
Route::post('delete-message', 'RestApi\MessageController@destroy');

// ****************************** BEGIN CHAT MODULE **************************************

// ****************************** END REWARD MODULE **************************************

Route::post('child-assign-finished-chores','RestApi\ChildHomeController@childAssignFinishedChores');
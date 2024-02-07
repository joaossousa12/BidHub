<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecoverPasswordController;


use App\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/home');

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::get('/auth/login', 'showLoginForm')->name('auth.login');
    Route::post('/auth/login', 'authenticate');
    Route::get('/auth/register', 'showRegistrationForm')->name('auth.register'); //TODO try to make /auth work for register and login and not auth/login and auth/register
    Route::post('/auth/register', 'register');
    Route::post('/auth/logout', 'logout')->name('auth.logout');
    Route::get('/auth/logout',function(){return redirect('/');}); // just to avoid errors when typing the url /auth/logout
});   

Route::get('password/reset', [RecoverPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [RecoverPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [RecoverPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [RecoverPasswordController::class, 'reset'])->name('password.update');

// Static Pages
Route::get('/about-us', function () {
    return view('pages.about');
});
Route::get('/main-features', function () {
    return view('pages.mainFeatures');
});

// Adminstration
Route::get('/adminDashboard', [AdminController::class, 'show']);
Route::post('/adminDashboard', [AdminController::class, 'updateAuctionStatus'])->name('adminDashboard.post');
Route::post('/adminDashboard/update-category', [AdminController::class, 'updateAuctionCategory'])->name('admin.updateAuctionCategory');
Route::post('/adminDashboard/createUser', [AdminController::class, 'createUser'])->name('admin.createUser');
Route::post('/adminDashboard/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
Route::post('/adminDashboard/ban/{id}', [AdminController::class, 'banUser'])->name('admin.banUser');
Route::post('/adminDashboard/unban/{id}', [AdminController::class, 'unbanUser'])->name('admin.unbanUser');
Route::post('/adminDashboard/store', [AdminController::class, 'storeCategory'])->name('admin.storeCategory');
Route::get('/search/users', [AdminController::class, 'search'])->name('search.users');


Route::get('/home', [HomeController::class, 'show'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');


Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');

Route::get('/auction/{id}', [AuctionController::class, 'show'])->name('auction.show');
Route::get('/auction/{id}/edit', [AuctionController::class, 'edit'])->name('auction.edit');
Route::post('/auction/{id}/edit', [AuctionController::class, 'submitEdit'])->name('auction.submitEdit');
Route::get('auction/{id}/cancel', function(){return redirect('/');});
Route::post('/auction/{id}/cancel', [AuctionController::class, 'cancelAuction'])->name('auction.cancelAuction');

Route::get('/users/{id}', [UserProfileController::class, 'showProfile'])->name('users.showProfile');
Route::get('/user/profile/createAuction', [UserProfileController::class, 'createAuctionView'])->name('createAuctionView');
Route::post('/user/profile/createAuction', [UserProfileController::class, 'createAuction'])->name('createAuction');
Route::get('/users/{id}/edit', [UserProfileController::class, 'edit'])->name('users.edit');
Route::post('/users/{id}/update', [UserProfileController::class, 'submitEdit'])->name('users.update');
Route::post('/users/delete', [UserProfileController::class, 'delete'])->name('users.delete');
Route::get('/faq', [FaqController::class, 'index']);
Route::post('/users/addCredit/{id}', [UserProfileController::class, 'addCredit']);
Route::post('/users/{id}/uploadProfilePicture', [UserProfileController::class, 'uploadProfilePicture'])->name('users.uploadProfilePicture');

Route::post('/bid', [BidController::class, 'store'])->name('bid.store');

Route::post('/auction/{id}/follow', [AuctionController::class, 'addToWishlist'])->name('auction.follow');
Route::post('/auction/{id}/unfollow', [AuctionController::class, 'removeFromWishlist'])->name('auction.unfollow');

Route::post('/update-notification-status', [NotificationController::class, 'updateStatus'])->name('updateNotificationStatus');
Route::get('/auction/time-end/{id}', [AuctionController::class, 'timeEnd'])->name('auction.timeEnd');
Route::get('/auction/time-ending/{id}', [AuctionController::class, 'timeEnding'])->name('auction.timeEnding');
Route::get('/auction/winner/{id}', [AuctionController::class, 'winner'])->name('auction.winner');
Route::post('/update-notification-status/{id}', [NotificationController::class, 'updateStatus'])->name('notification.updateStatus');

Route::post('/rate-seller/{sellerId}', [UserProfileController::class, 'rateSeller'])->name('rateSeller');


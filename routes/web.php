<?php

// Keep existing imports
use Illuminate\Support\Facades\Route;
// Add the new controller import
use App\Http\Controllers\Admin\UserController; // Make sure this namespace is correct

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

// --- Existing Kanka Routes ---
// --- PASTE YOUR ORIGINAL KANKA ROUTES HERE ---
// --- (Make sure you don't delete them!) ---
Route::get('/auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback')->name('auth.provider.callback');

Route::group(['prefix' => 'subscription-api'], function () {
    Route::get('setup-intent', 'Settings\SubscriptionApiController@setupIntent');
    Route::post('payments', 'Settings\SubscriptionApiController@paymentMethods');
    Route::get('payment-methods', 'Settings\SubscriptionApiController@getPaymentMethods');
    Route::post('remove-payment', 'Settings\SubscriptionApiController@removePaymentMethod');
    Route::get('check-coupon/{tier}', [App\Http\Controllers\Settings\SubscriptionApiController::class, 'checkCoupon'])
        ->name('subscription.check-coupon');
});

Route::post(
    'stripe/webhook',
    '\App\Http\Controllers\WebhookController@handleWebhook'
)->name('cashier.webhook');

Route::get('users/{user}', [App\Http\Controllers\User\ProfileController::class, 'show'])->name('users.profile');

Route::get('/_ccapi/country', [App\Http\Controllers\CookieConsentController::class, 'index'])
    ->name('cookieconsent.country');

Route::get('/frontend-prepare', [App\Http\Controllers\FrontendPrepareController::class, 'index']);

Route::get('/_setup', [App\Http\Controllers\SetupController::class, 'index']);
Route::get('/up', [App\Http\Controllers\HealthController::class, 'index']);

Route::model('feature', App\Models\Feature::class);
Route::get('roadmap', [App\Http\Controllers\Roadmap\RoadmapController::class, 'index'])->name('roadmap');
Route::get('roadmap/{feature}', [App\Http\Controllers\Roadmap\FeatureController::class, 'show'])->name('roadmap.feature.show');
Route::post('roadmap/{feature}/upvote', [App\Http\Controllers\Roadmap\FeatureController::class, 'upvote'])->name('roadmap.upvote');
Route::post('roadmap/submit', [App\Http\Controllers\Roadmap\FeatureController::class, 'store'])->name('roadmap.store');

Route::get('/validation/{userValidation}', [App\Http\Controllers\User\EmailValidationController::class, 'validateEmail'])->name('validation.email');

// Game System Search
Route::get('/search/systems', [App\Http\Controllers\Search\GameSystemSearchController::class, 'index'])->name('search.systems');
// --- End Existing Kanka Routes ---


// --- ADMIN USER MANAGEMENT ROUTES ---
// Group admin routes and apply AUTH middleware.
// Admin authorization MUST be checked inside the UserController methods.
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Route for displaying the user list page
    // Corresponds to the index() method in Admin/UserController
    // Accessible via URL: /admin/users
    // Route name: admin.users.index
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Route for handling the user update form submission (using PUT method)
    // Corresponds to the update() method in Admin/UserController
    // Accessible via URL: /admin/users/{user_id} (with PUT request)
    // Route name: admin.users.update
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // --- Placeholder routes for other actions (implement controller methods as needed) ---

    // Example route for showing the password change form
    // Route::get('/users/{user}/password', [UserController::class, 'editPassword'])->name('users.password.edit');

    // Example route for handling the password update submission
    // Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');

    // Example route for deleting a user (using DELETE method)
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

});
// --- END ADMIN USER MANAGEMENT ROUTES ---
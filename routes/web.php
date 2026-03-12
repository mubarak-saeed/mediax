<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StreamEpisodeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WatchController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية والأقسام والمحتوى والبحث (عام)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
Route::get('sections/{section:slug}', [SectionController::class, 'show'])->name('sections.show');
Route::get('sections/{section:slug}/{subsection:slug}', [SectionController::class, 'showSubsection'])->name('sections.subsection.show');
Route::get('content/{content}', [ContentController::class, 'show'])->name('content.show');
Route::get('content/{content}/season/{season}', [SeasonController::class, 'show'])->name('seasons.show');
Route::get('content/{content}/season/{season}/episode/{episode}', [EpisodeController::class, 'show'])->name('episodes.show');
Route::get('search', SearchController::class)->middleware('throttle:search')->name('search');

// خطط الاشتراك (عام)
Route::get('plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
Route::get('api/users', function () {
    $users = User::all();
    $data = [
        'status' => 'success',
        'data' => $users
    ];
    return response()->json($data);
});
// الضيوف: تسجيل الدخول والتسجيل و OAuth ونسيت كلمة المرور (مع حد الطلبات)
Route::middleware('guest')->group(function (): void {
    
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
    Route::get('forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
});
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// المستخدم المصادق: الملف الشخصي، المفضلة، الاشتراك
Route::middleware('auth')->group(function (): void {
    Route::get('dashboard', fn () => redirect()->route('profile', request()->query()))->name('dashboard');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('favorites', [FavoritesController::class, 'index'])->name('favorites');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::post('subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('subscription/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('subscription.billing-portal');
});

// المشاهدة والقراءة (يتطلب اشتراكاً أو أدمن)
Route::middleware(['auth', 'subscriber'])->group(function (): void {
    Route::get('watch/{content}', [WatchController::class, 'show'])->name('watch.show');
    Route::get('read/{content}', [ReadController::class, 'show'])->name('read.show');
});

// البث الموقّع (للمشاهدة/التحميل)
Route::get('stream/{content}', StreamController::class)
    ->middleware(['auth', 'subscriber', 'signed'])
    ->name('stream');
Route::get('stream/episode/{episode}', StreamEpisodeController::class)
    ->middleware(['auth', 'subscriber', 'signed'])
    ->name('stream.episode');

<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WishlistController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('cards', [WebsiteController::class, 'searchCards'])->name('cards.search');

Route::get('card', [WebsiteController::class, 'showCard'])->name('card');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('cards/add', [DashboardController::class, 'addCards'])->name('cards.add');
    Route::post('card/update', [DashboardController::class, 'updateCard'])->name('card.update');
    Route::post('card/delete', [DashboardController::class, 'deleteCard'])->name('card.delete');
    Route::post('card/toggle-private', [DashboardController::class, 'togglePrivate'])->name('card.toggle-private');
    Route::post('card/toggle-foil', [DashboardController::class, 'toggleFoil'])->name('card.toggle-foil');

    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('toggle-wishlist', [WishlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::post('wishlist/bulk-add', [WishlistController::class, 'addCards'])->name('wishlist.add');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
// Legacy route - keeping for backward compatibility
Route::get('/admin/categories/update-order', [HomeController::class, 'updateOrder'])->name('cat.updateOrder');

// Category Management Routes
Route::prefix('admin/categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/hierarchical', [CategoryController::class, 'getHierarchicalCategories'])->name('hierarchical');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::put('/order', [CategoryController::class, 'updateOrder'])->name('updateOrder');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulkDelete');
    Route::post('/bulk-move', [CategoryController::class, 'bulkMove'])->name('bulkMove');
    Route::post('/expand-state', [CategoryController::class, 'saveExpandState'])->name('saveExpandState');
    Route::get('/expand-state', [CategoryController::class, 'getExpandState'])->name('getExpandState');
    Route::delete('/expand-state', [CategoryController::class, 'resetExpandState'])->name('resetExpandState');
    Route::get('/search', [CategoryController::class, 'search'])->name('search');
});

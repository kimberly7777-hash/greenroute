<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get(uri: '/', action: function (): View {
    return view(view: 'welcome');
});
Route::get(uri: '/product',action: [ProductController::class,'index'])->name(name: 'product.index'); 
Route::get(uri: '/product/create',action: [ProductController::class,'create'])->name(name: 'product.create'); 
Route::post(uri: '/products',action: [ProductController::class,'store'])->name(name: 'product.store');
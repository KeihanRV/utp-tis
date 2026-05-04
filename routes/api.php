<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * title="Togem API Documentation",
 * version="1.0.0",
 * description="Dokumentasi API untuk mengelola produk game di Togem. 
 * API ini memungkinkan pengguna untuk melakukan operasi CRUD pada produk game, termasuk penambahan, 
 * pembaruan, penghapusan, dan pengambilan data produk. Setiap produk memiliki atribut seperti ID, nama, 
 * harga, stok, dan daftar DLC yang tersedia.",
 * contact={
 *     "name": "Togem Support",
 *     "email": "togem@gmail.com",
 *    "url": "https://www.togem.com/support"
 *  }
 * )
 */

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use\App\Http\Controllers\UserController;
use\App\Http\Controllers\ItemController;
use\App\Http\Controllers\CustomerController;
use\App\Http\Controllers\InvoiceController;
use\App\Http\Controllers\SaleController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/register', [UserController::class,'create']);
Route::post('/login', [UserController::class,'login']);


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', [UserController::class,'getUser']);
    Route::post('/logout', [UserController::class,'logoutUser']);
    
    Route::get('/items', [ItemController::class,'getItems']);
    Route::post('/items', [ItemController::class,'createItem']);
    Route::put('/items/{id}', [ItemController::class,'editItem']);
    Route::delete('/items/{id}', [ItemController::class,'removeItem']);

    Route::get('/customers', [CustomerController::class,'getCustomers']);
    Route::post('/customers', [CustomerController::class,'addCustomer']);
    Route::put('/customers/{id}', [CustomerController::class,'editCustomer']);
    Route::delete('/customers/{id}', [CustomerController::class,'removeCustomer']);

    Route::get('/invoices', [InvoiceController::class,'getInvoices']);
    Route::post('/invoices', [InvoiceController::class,'createInvoice']);
    Route::put('/invoices/{id}', [InvoiceController::class,'editInvoice']);
    Route::delete('/invoices/{id}', [InvoiceController::class,'removeInvoice']);

    Route::get('/sales-items', [SaleController::class,'getSalesItems']);
    Route::post('/sales-items', [SaleController::class,'addSalesItems']);
    Route::put('/sales-items/{id}', [SaleController::class,'editSalesItems']);
    Route::delete('/sales-items/{id}', [SaleController::class,'removeSalesItems']);

});


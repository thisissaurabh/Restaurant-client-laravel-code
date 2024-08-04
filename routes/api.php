<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\AddonController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ExpenseController;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/updateRestaurant', [UserController::class, 'updateRestaurant']);



    // Category
    Route::get('/getCategory', [CategoryController::class, 'index']);
    Route::post('/addCategory', [CategoryController::class, 'storeCategory']);
    Route::post('/editCategory/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/deleteCategory/{id}', [CategoryController::class, 'deleteCategory']);
    Route::get('/getSubcategories/{id}', [CategoryController::class, 'getSubcategories']);
    Route::post('/addSubcategory', [CategoryController::class, 'storeSubcategory']);
    Route::delete('/deleteSubcategory/{id}', [CategoryController::class, 'deleteSubcategory']);


    // food
    Route::get('/getfoods', [FoodController::class, 'index']);
    Route::post('/storefood', [FoodController::class, 'storefood']);
    Route::post('/updatefood/{id}', [FoodController::class, 'updatefood']);
    Route::delete('/deletefood/{id}', [FoodController::class, 'deleteFood']);
    Route::get('/foodsDetails/{id}', [FoodController::class, 'foodsDetails']);
    Route::get('/getItemReport', [FoodController::class, 'getItemReport']);

    // addons
    Route::get('addons', [AddonController::class, 'index']);
    Route::post('addAddons', [AddonController::class, 'store']);
    Route::get('addons/{id}', [AddonController::class, 'show']);
    Route::post('updateAddons/{id}', [AddonController::class, 'update']);
    Route::delete('addons/{id}', [AddonController::class, 'destroy']);
    // coupons

    Route::get('/coupons', [CouponController::class, 'index']);
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::get('/coupons/{id}', [CouponController::class, 'show']);
    Route::put('/coupons/{id}', [CouponController::class, 'update']);
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);


    // order invoice
    Route::post('/generate-invoice', [InvoiceController::class, 'generateInvoice']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'getInvoice']);
    Route::get('/invoices', [InvoiceController::class, 'getAllInvoices']);
    Route::get('/sales-report', [InvoiceController::class, 'getSalesReport']);
    Route::get('/customer-transactions', [InvoiceController::class, 'getCustomerTransactions']);
    Route::get('/customer-details', [InvoiceController::class, 'getCustomerDetails']);

    // expenses

    Route::get('/expense-get', [ExpenseController::class, 'index']);
    Route::post('/expense-store', [ExpenseController::class, 'store']);
    Route::delete('/expense-delete/{id}', [ExpenseController::class, 'destroy']);
    Route::put('/expense-update/{id}', [ExpenseController::class, 'update']);
    Route::get('/expense-report', [ExpenseController::class, 'report']);

});

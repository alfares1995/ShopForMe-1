<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProductsController as CustomerProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CheckoutController;
use App\Models\Product;
use App\Models\Review;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/products', [CustomerProductsController::class, 'index'])->name('products.index');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/confirm', [CheckoutController::class, 'confirmStripePayment']);

Route::get('/order/success', function() {
    return inertia('customer/order/success');
})->name('order.success');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
/////////////////////////////////////////
Route::middleware(['auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->middleware(AdminMiddleware::class)
                ->group(function () {
    
    Route::resource('/category',CategoriesController::class);
    Route::resource('/brands', BrandsController::class);
    Route::resource('/users', UsersController::class);
    Route::resource('/product', ProductsController::class);
    Route::delete('/product/{product}/image/{image}', [ProductsController::class, 'deleteImage'])->name('product.image.destroy');
    Route::resource('/orders', OrdersController::class);
    Route::post('orders/{order}/refund', [OrdersController::class, 'refund'])->name('orders.refund');
    Route::resource('/coupons', CouponsController::class);
    Route::resource('/reviews', ReviewController::class);
    Route::resource('/payments', PaymentController::class);
    Route::resource('/shipping-methods', ShippingMethodController::class);
    

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [MessageController::class, 'index'])->name('chat');
    Route::post('/messages', [MessageController::class, 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

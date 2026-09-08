<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WelcomeController;
use App\Models\User;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\SePayWebhookController;

Route::post('/sepay/webhook', SePayWebhookController::class)->name('sepay.webhook');
Route::post('/sepay/ipn', [SePayWebhookController::class, 'ipn'])->name('sepay.ipn');
Route::get('/sepay/callback/{result}', function (string $result) {
    abort_unless(in_array($result, ['success', 'error', 'cancel'], true), 404);

    return redirect()->route('welcome')->with(
        $result === 'success' ? 'success' : 'error',
        $result === 'success'
            ? 'SePay đã tiếp nhận thanh toán. Đơn hàng sẽ được cập nhật sau khi IPN xác nhận.'
            : 'Thanh toán SePay chưa hoàn tất.'
    );
})->name('sepay.callback');

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/categories', [CategoryController::class, 'publicIndex'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'publicShow'])->name('categories.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
        $user = User::findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);
        $user->markEmailAsVerified();

        return redirect()->route('welcome')->with('success', 'Email của bạn đã được xác thực.');
    })->withoutMiddleware('auth')->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'publicIndex'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'publicShow'])->name('products.show');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/checkout/bank-transfer', [OrderController::class, 'bankTransfer'])->name('checkout.bank-transfer');
});

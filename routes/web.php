<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\Brand2Controller;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\Category2Controller;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Product2Controller;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Client\CategoryClientController;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Client\BrandClientController;
use App\Http\Controllers\Client\AboutController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\CouponController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Client\AuthController;

// ========================== CART ==========================
Route::get('/cart/delete/{key}', [CartController::class, 'del'])->name('cartdel');
Route::post('/cart/update/{key}', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::post('/cartadd/{id}', [CartController::class, 'add'])->name('cartadd');
Route::post('/cartsave', [CartController::class, 'save'])->name('cartsave');
Route::get('/cartshow', fn() => view('client.cart.cartshow'))->name('cartshow');
Route::get('/cartcheckout', fn() => view('client.cart.checkout'))->name('checkout');

// ========================== AUTH CLIENT ==========================
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========================== REVIEW ==========================
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('products.reviews.store');

// ========================== CHECKOUT (CLIENT) ==========================
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index']);
});

// ========================== REPORT ==========================
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/revenue', [ReportController::class, 'revenue'])->name('report.revenue');
});

// ========================== ORDERS (ADMIN) ==========================
Route::middleware(['web', 'auth'])->prefix('admin')->name('ad.')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// ========================== CHECKOUT ROUTES ==========================
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/momo-payment/{orderId}', [CheckoutController::class, 'momoPayment'])->name('momo-payment');
    Route::post('/confirm-momo/{orderId}', [CheckoutController::class, 'confirmMomoPayment'])->name('confirm-momo');
    Route::get('/check-payment/{orderId}', [CheckoutController::class, 'checkPaymentStatus'])->name('check-payment-status');
    Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');
});

// ========================== COUPON ==========================
Route::prefix('coupon')->name('coupon.')->group(function () {
    Route::post('/apply', [CouponController::class, 'apply'])->name('apply');
    Route::post('/remove', [CouponController::class, 'remove'])->name('remove');
});

// ========================== ABOUT ==========================
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/about/team', [AboutController::class, 'team'])->name('about.team');
Route::get('/about/contact', [AboutController::class, 'contact'])->name('about.contact');

// ========================== HOME ==========================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/Trang-chu', [HomeController::class, 'index'])->name('homepage');

// ========================== CATEGORY & BRAND (CLIENT) ==========================
Route::get('/category/{id}', [CategoryClientController::class, 'detail'])->name('category');
Route::get('/brand/{id}', [BrandClientController::class, 'detail'])->name('brand');

// ========================== PRODUCTS (CLIENT) ==========================
Route::prefix('products')->name('client.products.')->group(function () {
    Route::get('/', [ProductClientController::class, 'index'])->name('index');
    Route::get('/detail/{id}', [ProductClientController::class, 'detail'])->name('detail');
    Route::get('/search', [ProductClientController::class, 'search'])->name('search');
});

// ========================== ADMIN AUTH ==========================
Route::get('/admin/login', [UserController::class, 'login'])->name('ad.login');
Route::post('/admin/login', [UserController::class, 'loginpost'])->name('ad.loginpost');
Route::get('/admin/forgotpass', [UserController::class, 'forgotpassform'])->name('ad.forgotpass');
Route::post('/admin/forgotpass', [UserController::class, 'forgotpass'])->name('ad.forgotpasspost');
Route::get('/admin/resetpass/{id}', [UserController::class, 'showResetForm'])->name('ad.reset.form');
Route::post('/admin/resetpass/{id}', [UserController::class, 'handleReset'])->name('ad.reset');

// ========================== ADMIN DASHBOARD ==========================
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('ad.dashboard');

// ========================== ADMIN (GROUPED ROUTES) ==========================
Route::prefix('admin')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('ad.dashboard');

    // Customers
    Route::name('ad.')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::get('/customers/edit', [CustomerController::class, 'create'])->name('customers.edit');
        Route::get('/customers/destroy', [CustomerController::class, 'create'])->name('customers.destroy');

        Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
        Route::get('/changepass', [UserController::class, 'showChangePassForm'])->name('changepass.form');
        Route::post('/changepass', [UserController::class, 'changepass'])->name('changepass');
    });

    // Categories (Query Builder)
    Route::name('cate.')->middleware('roles:1')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/categories/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/categories/{id}/delete', [CategoryController::class, 'delete'])->name('delete');
    });

    // Categories (Eloquent)
    Route::name('cate2.')->middleware('roles:1')->group(function () {
        Route::get('/categories-2', [Category2Controller::class, 'index'])->name('index');
        Route::get('/categories-2/create', [Category2Controller::class, 'create'])->name('create');
        Route::post('/categories-2/store', [Category2Controller::class, 'store'])->name('store');
        Route::get('/categories-2/{id}/edit', [Category2Controller::class, 'edit'])->name('edit');
        Route::post('/categories-2/{id}', [Category2Controller::class, 'update'])->name('update');
        Route::delete('/categories-2/{id}/delete', [Category2Controller::class, 'delete'])->name('delete');
    });

    // Brands (Query Builder)
    Route::name('brand.')->middleware('roles:1')->group(function () {
        Route::get('/brands', [BrandController::class, 'index'])->name('index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('create');
        Route::post('/brands/store', [BrandController::class, 'store'])->name('store');
        Route::get('/brands/{id}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::post('/brands/{id}', [BrandController::class, 'update'])->name('update');
        Route::delete('/brands/{id}/delete', [BrandController::class, 'delete'])->name('delete');
    });

    // Brands (Eloquent)
    Route::name('brand2.')->middleware('roles:1')->group(function () {
        Route::get('/brands-2', [Brand2Controller::class, 'index'])->name('index');
        Route::get('/brands-2/create', [Brand2Controller::class, 'create'])->name('create');
        Route::post('/brands-2/store', [Brand2Controller::class, 'store'])->name('store');
        Route::get('/brands-2/{id}/edit', [Brand2Controller::class, 'edit'])->name('edit');
        Route::post('/brands-2/{id}', [Brand2Controller::class, 'update'])->name('update');
        Route::delete('/brands-2/{id}/delete', [Brand2Controller::class, 'delete'])->name('delete');
    });

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('pro.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('pro.create');
    Route::post('/products/store', [ProductController::class, 'store'])->name('pro.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('pro.edit');
    Route::post('/products/{id}', [ProductController::class, 'update'])->name('pro.update');
    Route::post('/products/{id}/delete', [ProductController::class, 'delete'])->name('pro.delete');

    // Products (Eloquent)
    Route::prefix('products-2')->name('pro2.')->group(function () {
        Route::get('/', [Product2Controller::class, 'index'])->name('index');
        Route::get('/create', [Product2Controller::class, 'create'])->name('create');
        Route::post('/store', [Product2Controller::class, 'store'])->name('store');
        Route::get('/{id}/edit', [Product2Controller::class, 'edit'])->name('edit');
        Route::post('/{id}', [Product2Controller::class, 'update'])->name('update');
        Route::post('/{id}/delete', [Product2Controller::class, 'delete'])->name('delete');
    });

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
});

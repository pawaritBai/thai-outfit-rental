<?php

use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\OutfitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Models\CartItem;
use App\Models\SelectService;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;

use function Laravel\Prompts\search;

Route::get('/', [OutfitController::class, 'index'])->name('outfits.index');
Route::prefix('outfits')->name('outfits.')->group(function(){
    Route::get('/search/{searchkey}',[OutfitController::class, 'searchOutfits'])->name('search');
});

Route::get('/dashboard', function () {
    // ตรวจสอบประเภทผู้ใช้และเปลี่ยนเส้นทางตามความเหมาะสม
    if (auth()->check()) {
        if (auth()->user()->userType == 'shop owner') {
            return redirect()->route('shopowner.shops.my-shop');
        }
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// check if account active
Route::middleware(['is_active'])->group(function () {

// report system
Route::get('/report-issue', [IssueController::class, 'create'])->name('report.create');
Route::post('/report-issue', [IssueController::class, 'reportPage'])->name('report.issue');
Route::get('/issue-show', [IssueController::class, 'showReportStatus'])->name('issue.show');
Route::get('/issue-reported/{id}', [IssueController::class, 'issueReported'])->name('issue.reported');


// make-up
Route::prefix('make-upartist')->name('make-upartist.')->middleware('auth', 'is_makeup')->group(function () {
    Route::get('/', [StaffController::class, 'schedule'])->name('dashboard');
    Route::get('/work/details/{id}', [StaffController::class, 'workDetails'])->name('work.details');
    Route::post('/work/finish/{id}', [StaffController::class, 'finishJob'])->name('work.finish');
    Route::get('/work-list', [StaffController::class, 'getAvailableJobs'])->name('work-list');
    Route::get('/earning', [StaffController::class, 'earning'])->name('work.earning');

    Route::post('/accept-job', [StaffController::class, 'acceptJob'])->name('accept-job');

    Route::prefix('issue')->name('issue.')->group(function () {
        Route::get('/', [IssueController::class, 'workIndex'])->name('index');
        Route::get('/create', [IssueController::class, 'workCreate'])->name('create');
        Route::post('/store', [IssueController::class, 'workStore'])->name('store');
        Route::get('/{id}', [IssueController::class, 'workShow'])->name('show');
    });
});

// photographer
Route::prefix('photographer')->name('photographer.')->middleware('auth', 'is_photographer')->group(function () {
    Route::get('/', [StaffController::class, 'schedule'])->name('dashboard');
    Route::get('/work/details/{id}', [StaffController::class, 'workDetails'])->name('work.details');
    Route::post('/work/finish/{id}', [StaffController::class, 'finishJob'])->name('work.finish');
    Route::get('/work-list', [StaffController::class, 'getAvailableJobs'])->name('work-list');
    Route::get('/earning', [StaffController::class, 'earning'])->name('work.earning');

    Route::post('/accept-job', [StaffController::class, 'acceptJob'])->name('accept-job');
    
    Route::prefix('issue')->name('issue.')->group(function () {
        Route::get('/', [IssueController::class, 'workIndex'])->name('index');
        Route::get('/create', [IssueController::class, 'workCreate'])->name('create');
        Route::post('/store', [IssueController::class, 'workStore'])->name('store');
        Route::get('/{id}', [IssueController::class, 'workShow'])->name('show');
    });
});


// admin
Route::prefix('admin')->name('admin.')->middleware('auth', 'is_admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    });

    Route::get('/dashboard',  [AdminController::class, 'index'])->name('dashboard');

    // เปลี่ยนสถานะผู้ใช้
    Route::post('/users/{user_id}/toggleStatus', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // แสดงรายการผู้ใช้
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/acceptance', [UserController::class, 'acceptance'])->name('users.acceptance');
    Route::post('/users/{user_id}/{status}', [UserController::class, 'updateStatus']); // error เกิดจากตัวนี้ !!!!!!!!!!!

    // แก้ไขผู้ใช้
    Route::get('/users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user_id}', [UserController::class, 'update'])->name('users.update');

    // shop
    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::post('/shops/{user_id}/toggleStatus', [ShopController::class, 'toggleStatus'])->name('shops.toggleStatus');

    Route::post('/shops/{user_id}/edit', [ShopController::class, 'edit'])->name('shops.edit');
    Route::put('/shops/{user_id}', [ShopController::class, 'update'])->name('shops.update');

    Route::get('/shops/acceptance', [ShopController::class, 'acceptance'])->name('shops.acceptance');
    Route::post('/shops/{shop_id}/updateStatus', [ShopController::class, 'updateStatus'])->name('shops.updateStatus'); // error เกิดจากตัวนี้ !!!!!!!!!!!
    

    //crud outfit
    Route::get('/outfits', [OutfitController::class, 'AdminIndex'])->name('outfits.adminindex');
    Route::get('/outfits/{id}/edit', [OutfitController::class, 'AdminEdit'])->name('outfits.edit');
    Route::PUT('/outfits/{id}', [OutfitController::class, 'AdminUpdate'])->name('outfits.update');
    Route::delete('/outfits/{id}', [OutfitController::class, 'destroy'])->name('outfits.destroy');
    
    //report issue
    Route::get('/issues', [IssueController::class, 'showNotifications'])->name('issue.show');
    Route::get('/issues/{id}/reply', [IssueController::class, 'replyPage'])->name('issue.replyPage');
    Route::post('/issues/{id}/reply', [IssueController::class, 'reply'])->name('issue.reply');
    Route::post('/issues/{id}/updateStatus', [IssueController::class, 'updateStatus'])->name('issue.updateStatus');

    //booking
    Route::get('/booking', [BookingController::class, 'adminBooking'])->name('booking.index');
    Route::get('/order/{id}', [BookingController::class, 'adminOrderDetails'])->name('booking.detail');

    //stat
    Route::get('/statistics/shop', [AdminController::class, 'showShopStatistics'])->name('statistics.shop');
    Route::get('/statistics/photographer', [AdminController::class, 'showPhotographerStatistics'])->name('statistics.photographer');
    Route::get('/statistics/make-up', [AdminController::class, 'showMakeupStatistics'])->name('statistics.make-upartist');

    //categories
    Route::resource('categories', CategoryController::class);

    Route::post('/admin/shops/{shop_id}/{status}', [ShopController::class, 'updateStatusAjax'])->name('admin.shops.updateStatusAjax');
    Route::post('/admin/users/{user_id}/{status}', [UserController::class, 'updateStatusAjax'])->name('admin.users.updateStatusAjax');
});



// shop owner
Route::get('/api/outfit-stock', [OutfitController::class, 'checkOutfitStock']);

Route::prefix('shopowner')->name('shopowner.')->middleware('auth', 'is_shopowner')->group(function () {
    // เปลี่ยนจาก dashboard เป็น redirect ไปที่ my-shop
    Route::get('/dashboard', function () {
        return redirect()->route('shopowner.shops.my-shop');
    })->name('dashboard');
    
    // จัดการร้านค้า
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/my-shop', [ShopController::class, 'myShop'])->name('shops.my-shop');
    Route::get('/shops/{shop_id}/edit-my-shop', [ShopController::class, 'editMyShop'])->name('shops.edit-my-shop');
    Route::put('/shops/{shop_id}/update-my-shop', [ShopController::class, 'updateMyShop'])->name('shops.update-my-shop');
    
    // จัดการชุด
    Route::get('/shop/costumes', [ShopController::class, 'listCostumes'])->name('shop.costumes');
    Route::get('/shop/new-form', [ShopController::class, 'newForm'])->name('shop.new-form');
    Route::post('/shop/store-costume', [ShopController::class, 'storeCostume'])->name('shop.store-costume');

    // Outfit Routes
    Route::get('/outfits', [OutfitController::class, 'shopOwnerIndex'])->name('outfits.index');
    Route::get('/outfits/create', [OutfitController::class, 'create'])->name('outfits.create');
    Route::post('/outfits', [OutfitController::class, 'store'])->name('outfits.store');
    Route::get('/outfits/{outfit}/edit', [OutfitController::class, 'edit'])->name('outfits.edit');
    Route::put('/outfits/{outfit}', [OutfitController::class, 'update'])->name('outfits.update');
    Route::delete('/outfits/{outfit}', [OutfitController::class, 'destroy'])->name('outfits.destroy');

    // Category management routes
    Route::resource('categories', CategoryController::class);

    // Add this directly in the shopowner group (not in a nested group)
    Route::resource('promotions', PromotionController::class);
    
    // API route for checking promo codes
    Route::post('/check-promotion-code', [PromotionController::class, 'checkPromoCode'])->name('promotions.check-code');
   
    // Bookings routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [BookingController::class, 'updateStatus'])->name('updateStatus');
    });

    // Stats routes
    Route::prefix('stats')->name('stats.')->group(function () {
        Route::get('/', [BookingController::class, 'stats'])->name('index');
        Route::get('/income', [BookingController::class, 'stats'])->name('income');
    });

    // Inside the shopowner prefix group
    Route::prefix('issue')->name('issue.')->group(function () {
        Route::get('/', [IssueController::class, 'shopownerIndex'])->name('index');
        Route::get('/create', [IssueController::class, 'shopownerCreate'])->name('create');
        Route::post('/store', [IssueController::class, 'shopownerStore'])->name('store');
        Route::get('/{id}', [IssueController::class, 'shopownerShow'])->name('show');
    });
    
    // Routes สำหรับจัดการชุดที่มีไม่เพียงพอ - MOVED INSIDE THE is_shopowner GROUP
    Route::get('/bookings/insufficient-stock', [BookingController::class, 'insufficientStock'])->name('bookings.insufficient-stock');
    Route::get('/bookings/{booking}/suggest-alternatives/{orderDetail}', [BookingController::class, 'suggestAlternatives'])->name('bookings.suggest-alternatives');
    Route::post('/bookings/save-selection', [BookingController::class, 'saveSelection'])->name('bookings.save-selection');
    
});

// Move this outside the shopowner group if you want it accessible to all users
Route::post('/api/check-promotion', [PromotionController::class, 'checkPromoCode']);

//auth
Route::middleware('auth')->group(function () {

    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    //Customer
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/editCus', [ProfileController::class, 'editCus'])->name('profile.editCus');

    Route::get('/profile/issue', [IssueController::class, 'CustomerIssue'])->name('profile.customer.issue');
    Route::get('/profile/issue/create', [IssueController::class, 'CustomerCreate'])->name('profile.customer.create');
    Route::get('/profile/customer/orderHistory', [ProfileController::class, 'orderHistory'])->name('profile.customer.orderHistory');
    Route::get('/profile/customer/orderDetail/{bookingId}', [ProfileController::class, 'orderDetail'])->name('profile.customer.orderDetail');

    Route::patch('/profile-edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile-edit', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // เพิ่ม route ใหม่สำหรับดูและตอบรับชุดทดแทน
    Route::get('/profile/customer/outfit-suggestions/{bookingId}', [ProfileController::class, 'outfitSuggestions'])
        ->name('profile.customer.outfit-suggestions');
    Route::post('/profile/customer/confirm-selection', [ProfileController::class, 'confirmSelection'])
        ->name('profile.customer.confirm-selection');
});

Route::middleware(['auth', 'is_customer'])->group(function () {
    Route::get('/outfit/all', [OutfitController::class, 'index'])->name('outfit.all');
});

Route::prefix('order')->name('order.')->group(function(){
    Route::post('/viewAddTo', [OrderController::class, 'viewAddTo'])->name('viewAddTo');
    Route::post('/store', [OrderController::class, 'store'])->name('store');

});

Route::prefix('orderdetail')->name('orderdetail.')->group(function(){
    Route::get('/outfit/{idOutfit}', [OrderDetailController::class, 'index'])->name('index');
    Route::post('/calculate-stock', [OrderDetailController::class, 'calculateStock'])->name('calculate.stock');
    Route::get('/test/{id}', [OrderDetailController::class, 'test'])->name('test');
    Route::match(['get', 'post'], '/viewAddTo', [OrderDetailController::class, 'viewAddTo'])->name('viewAddTo');
    Route::post('/orderdetail/addTo', [OrderDetailController::class, 'addTo'])->name('addTo');

});

// In web.php
Route::get('/outfits/category/{category}', [OutfitController::class, 'byCategory'])->name('outfits.category');

//search
Route::prefix('outfits')->name('outfits.')->group(function(){
    Route::get('/search', [OutfitController::class, 'searchOutfits'])->name('search');
});

Route::prefix('cartItem')->name('cartItem.')->group(function(){
    Route::post('/addToCart', [CartItemController::class, 'addToCart'])->name('cart.add');
    Route::get('/allItem',[CartItemController::class, 'index'])->name('allItem');

    Route::delete('/deleteItem', [CartItemController::class, 'deleteItem'])->name('deleteItem');


    Route::post('/updateAmount', [CartItemController::class, 'updateItem'])->name('updateItem');

});

Route::post('/api/check-promotion', [PromotionController::class, 'checkPromotion']);

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/form/{booking_id}/{cycle}', [PaymentController::class, 'showPaymentForm'])->name('form');
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
});

Route::prefix('/profile/customer/address')->name('profile.customer.address.')->middleware('auth', 'is_customer')->group(function () {
    Route::get('/', [ProfileController::class, 'customerAddress'])->name('index'); // แสดงรายการที่อยู่
    Route::get('/create', [ProfileController::class, 'createAddress'])->name('create'); // แบบฟอร์มเพิ่ม
    Route::post('/store', [ProfileController::class, 'storeAddress'])->name('store'); // บันทึกข้อมูลใหม่
    Route::get('/{cus_address_id}/edit', [ProfileController::class, 'editAddress'])->name('edit'); // แบบฟอร์มแก้ไข
    Route::put('/{cus_address_id}', [ProfileController::class, 'updateAddress'])->name('update'); // อัปเดต
    Route::delete('/{cus_address_id}', [ProfileController::class, 'deleteAddress'])->name('delete'); // ลบ
});

Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::get('/payments/{booking_id}', [PaymentController::class, 'viewUpdate'])->name('payment.viewUpdate');
Route::put('/payments/{payment}', [PaymentController::class, 'updateMethod'])->name('payment.updateMethod');
Route::get('/payments/cycle2/{booking_id}', [PaymentController::class, 'updateToCycle2'])->name('payment.createCycle2');
Route::get('/viewpayments/cycle2/{booking_id}', [PaymentController::class, 'viewCycle2'])->name('payment.viewCycle2');


});


require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Users\AdminController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::controller(ShopController::class)
    -> group(function (){
        Route::get('/shop', 'index')->name('shop.index');
        Route::get('/shop/{product_name}', 'product_details')->name('shop.product.details');
    });

Route::controller(CartController::class)
    -> group(function (){
        Route::get('/cart', 'index')->name('cart.index');
        Route::get('/checkout', 'checkout')->name('cart.checkout');
        Route::get('/order-confirmation', 'order_confirmation')->name('cart.order.confirmation');
        Route::post('/cart/add', 'add_to_cart')->name('cart.add');
        Route::post('/place-an-order', 'place_an_order')->name('cart.place.an.order');
        Route::post('/cart/apply-coupon', 'apply_coupon_code')->name('cart.coupon.apply');
        Route::put('/cart/increase-quantity/{rowId}', 'increase_cart_quantity')->name('cart.qty.increase');
        Route::put('/cart/decrease-quantity/{rowId}', 'decrease_cart_quantity')->name('cart.qty.decrease');
        Route::delete('/cart/clear', 'empty_cart')->name('cart.empty');
        Route::delete('/cart/remove/{rowId}', 'remove_item')->name('cart.item.remove');
        Route::delete('/cart/remove-coupon', 'remove_coupon_code')->name('cart.coupon.remove');
    });

Route::controller(WishlistController::class)
    -> group(function (){
        Route::get('/wishlist', 'index')->name('wishlist.index');
        Route::post('/wishlist/add', 'add_to_wishlist')->name('wishlist.add');
        Route::post('/wishlist/move-to-cart/{rowId}', 'move_to_cart')->name('wishlist.move.to.cart');
        Route::delete('/wishlist/clear', 'empty_wishlist')->name('wishlist.items.clear');
        Route::delete('/wishlist/item/remove/{rowId}', 'remove_item')->name('wishlist.item.remove');
    });

Route::controller(UserController::class)
    -> middleware(['auth'])
    -> group(function (){
        Route::get('/account-dashboard', 'index')->name('user.index');
        Route::get('/account-orders', 'orders')->name('user.orders');
        Route::get('/account-order/{id}', 'order_details')->name('user.order.details');
        Route::put('/account-order/cancel-order', 'order_cancel')->name('user.order.cancel');
    });

Route::middleware(['auth', Admin::class])
    -> controller(AdminController::class)
    -> group(function (){
        Route::get('/admin', 'index')->name('admin.index');
        // Brands
        Route::get('/admin/brands', 'brands')->name('admin.brands');
        Route::get('/admin/brands/add', 'add_brands')->name('admin.brand.add');
        Route::post('/admin/brands/store', 'brand_store')->name('admin.brand.store');
        Route::get('/admin/brands/edit/{id}', 'edit_brand')->name('admin.brand.edit');
        Route::put('/admin/brands/update', 'brand_update')->name('admin.brand.update');
        Route::delete('/admin/brands/{id}/delete', 'delete_brand')->name('admin.brand.delete');
        // Categories
        Route::get('/admin/categories', 'categories')->name('admin.categories');
        Route::get('/admin/category/add', 'add_categories')->name('admin.category.add');
        Route::post('/admin/category/store', 'category_store')->name('admin.category.store');
        Route::get('/admin/category/edit/{id}', 'edit_category')->name('admin.category.edit');
        Route::put('/admin/category/update', 'category_update')->name('admin.category.update');
        Route::delete('/admin/category/{id}/delete', 'delete_category')->name('admin.category.delete');
        // Products
        Route::get('/admin/products', 'products')->name('admin.products');
        Route::get('/admin/product/add', 'add_products')->name('admin.product.add');
        Route::post('/admin/product/store', 'product_store')->name('admin.product.store');
        Route::get('/admin/product/edit/{id}', 'edit_product')->name('admin.product.edit');
        Route::put('/admin/product/update', 'product_update')->name('admin.product.update');
        Route::delete('/admin/product/{id}/delete', 'delete_product')->name('admin.product.delete');
        // Warehouse
        Route::get('/admin/warehouses', 'warehouses')->name('admin.warehouses');
        Route::get('/admin/warehouse/add', 'add_warehouses')->name('admin.warehouse.add');
        Route::post('/admin/warehouse/store', 'warehouse_store')->name('admin.warehouse.store');
        Route::get('/admin/warehouse/edit/{id}', 'edit_warehouse')->name('admin.warehouse.edit');
        Route::put('/admin/warehouse/update', 'warehouse_update')->name('admin.warehouse.update');
        Route::delete('/admin/warehouse/{id}/delete', 'delete_warehouse')->name('admin.warehouse.delete');
        // Inventory
        Route::get('/admin/inventories', 'inventories')->name('admin.inventories');
        Route::get('/admin/inventory/add', 'add_inventories')->name('admin.inventory.add');
        Route::post('/admin/inventory/store', 'inventory_store')->name('admin.inventory.store');
        Route::get('/admin/inventory/edit/{id}', 'edit_inventory')->name('admin.inventory.edit');
        Route::put('/admin/inventory/update', 'inventory_update')->name('admin.inventory.update');
        Route::delete('/admin/inventory/{id}/delete', 'delete_inventory')->name('admin.inventory.delete');
        // Coupon
        Route::get('/admin/coupons', 'coupons')->name('admin.coupons');
        Route::get('/admin/coupon/add', 'add_coupon')->name('admin.coupon.add');
        Route::post('/admin/coupon/store', 'coupon_store')->name('admin.coupon.store');
        Route::get('/admin/coupon/edit/{id}', 'edit_coupon')->name('admin.coupon.edit');
        Route::put('/admin/coupon/update', 'coupon_update')->name('admin.coupon.update');
        Route::delete('/admin/coupon/{id}/delete', 'delete_coupon')->name('admin.coupon.delete');
        // Order
        Route::get('/admin/orders', 'orders')->name('admin.orders');
        Route::get('/admin/order/{id}/details', 'order_details')->name('admin.order.details');
        Route::put('/admin/order/update-status', 'update_order_stauts')->name('admin.order.status.update');
    });

Route::middleware(['auth'])
    -> group(function (){
        Route::get('/res', [HomeController::class, 'nice']);
    });

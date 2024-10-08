<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    ////////////         {-- Index --}}          ///////////////////
    public function index(): Factory|View|Application
    {
        $orders = Order::orderBy('created_at', 'desc')
            -> paginate(12);

        $delivered_sum = Order::where('status', 'delivered')
            -> sum('total');

        $canceled_sum = Order::where('status', 'canceled')
            -> sum('total');

        $ordered_sum = Order::where('status', 'ordered')
            -> sum('total');

        // Fetching the last 7 days of order revenue
        $orders_chart = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->groupBy('date')
            ->get();

        // Prepare data for chart.js
        $labels = $orders_chart->pluck('date');   // Extracting the dates
        $data = $orders_chart->pluck('total');

        return view('admin.index', compact('orders', 'delivered_sum', 'canceled_sum', 'ordered_sum', 'labels', 'data'));
    }


    ////////////         {-- Brands --}}          ///////////////////
    public function brands(): Factory|View|Application
    {
        $brands = Brand::orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.brands', compact('brands'));
    }

    public function add_brands(): Factory|View|Application
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $image = $request->file('image');

        $file_extension = $image->extension();

        $file_name = Carbon::now()->timestamp.'.'.$file_extension;

        $this->GenerateBrandThumbnailsImage($image, $file_name);

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $file_name,
        ]);

        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully!');
    }

    public function edit_brand($id): Factory|View|Application
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $file_name = $brand->image;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands').'/'.$brand->image))
                File::delete(public_path('uploads/brands').'/'.$brand->image);

            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
        }

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $file_name,
        ]);
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }

    public function delete_brand($id): RedirectResponse
    {
        $brand = Brand::find($id);

        if (File::exists(public_path('uploads/brands').'/'.$brand->image))
            File::delete(public_path('uploads/brands').'/'.$brand->image);

        $brand
            ->delete();

        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully!');
    }


    ////////////         {-- Categories --}}          ///////////////////
    public function categories(): Factory|View|Application
    {
        $categories = Category::orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.categories', compact('categories'));
    }

    public function add_categories(): Factory|View|Application
    {
        $categories = Category::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.category-add', compact('categories'));
    }

    public function category_store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'description' => 'required',
            'level' => 'required',
            'path' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'product_id',
        ]);

        $image = $request->file('image');

        $file_extension = $image->extension();

        $file_name = Carbon::now()->timestamp.'.'.$file_extension;

        $this->GenerateCategoryThumbnailsImage($image, $file_name);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'level' => $request->level,
            'path' => $request->path,
            'parent_id' => $request->parent_id,
            'image' => $file_name,
        ]);

        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
    }

    public function edit_category($id): Factory|View|Application
    {
        $category = Category::find($id);

        $categories = Category::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.category-edit', compact('category', 'categories'));
    }

    public function category_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'description' => 'required',
            'level' => 'required',
            'path' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'product_id',
        ]);

        $category = Category::find($request->id);
        $file_name = $category->image;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories').'/'.$category->image))
                File::delete(public_path('uploads/categories').'/'.$category->image);

            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'level' => $request->level,
            'path' => $request->path,
            'parent_id' => $request->parent_id,
            'image' => $file_name,
        ]);
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function delete_category($id): RedirectResponse
    {
        $category = Category::find($id);

        if (File::exists(public_path('uploads/categories').'/'.$category->image))
            File::delete(public_path('uploads/categories').'/'.$category->image);

        $category
            ->delete();

        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
    }


    ////////////         {-- Products --}}          ///////////////////
    public function products(): Factory|View|Application
    {
        $products = Product::orderBy('created_at', 'desc')
            -> paginate(10);

        return view('admin.products', compact('products'));
    }

    public function add_products(): Factory|View|Application
    {
        $categories = Category::select('id', 'name')
            -> orderBy('name')
            -> get();

        $brands = Brand::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.product-add', compact(['categories', 'brands']));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'description' => 'required',
            'UPC' => 'required|unique:products,UPC',
            'regular_price' => 'required|integer',
            'sales_price' => 'required|integer',
            'is_active' => 'required',
            'is_new_arrival' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $current_timestamp = Carbon::now()->timestamp;
        $imageName = '';

        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
        }

        $gallary_arr = array();
        $gallary_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file)
            {
                $gextension = $file->getClientOriginalName();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if(!$gcheck)
                {
                    $gfileName = $current_timestamp.'.'.$counter.'.'.$gextension;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    $gallary_arr[] = $gfileName;
                    $counter = $counter + 1;
                }
            }
            $gallary_images = implode(',', $gallary_arr);
        }

        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'image' => $imageName,
            'images' => $gallary_images,
            'regular_price' => $request->regular_price,
            'sales_price' => $request->sales_price,
            'UPC' => $request->UPC,
            'is_active' => $request->is_active,
            'is_new_arrival' => $request->is_new_arrival,
        ]);

        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }

    public function edit_product($id): Factory|View|Application
    {
        $product = Product::find($id);

        $categories = Category::select('id', 'name')
            -> orderBy('name')
            -> get();

        $brands = Brand::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'description' => 'required',
            'UPC' => 'required|unique:products,UPC',
            'regular_price' => 'required|integer',
            'sales_price' => 'required|integer',
            'is_active' => 'required',
            'is_new_arrival' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        $product = Product::find($request->id);
        $current_timestamp = Carbon::now()->timestamp;
        $imageName = '';

        if($request->hasFile('image'))
        {
            if(File::exists(public_path('upload/products').'/'.$product->image))
                File::delete(public_path('upload/products').'/'.$product->image);

            if(File::exists(public_path('upload/products/thumbnails').'/'.$product->image))
                File::delete(public_path('upload/products/thumbnails').'/'.$product->image);

            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
        }

        $gallary_arr = array();
        $gallary_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            foreach (explode(',', $product->imgeas) as $ofile)
            {
                if(File::exists(public_path('upload/products').'/'.$ofile))
                    File::delete(public_path('upload/products').'/'.$ofile);

                if(File::exists(public_path('upload/products/thumbnails').'/'.$ofile))
                    File::delete(public_path('upload/products/thumbnails').'/'.$ofile);
            }
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file)
            {
                $gextension = $file->getClientOriginalName();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if($gcheck)
                {
                    $gfileName = $current_timestamp.'.'.$counter.'.'.$gextension;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    $gallary_arr[] = $gfileName;
                    $counter = $counter + 1;
                }
            }
            $gallary_images = implode(',', $gallary_arr);
        }

        $product
            ->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'image' => $imageName,
            'images' => $gallary_images,
            'regular_price' => $request->regular_price,
            'sales_price' => $request->sales_price,
            'UPC' => $request->UPC,
            'is_active' => $request->is_active,
            'is_new_arrival' => $request->is_new_arrival,
        ]);

        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function delete_product($id): RedirectResponse
    {
        $product = Product::find($id);

        if (File::exists(public_path('upload/products').'/'.$product->image))
            File::delete(public_path('upload/products').'/'.$product->image);

        $product
            ->delete();

        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
    }


    ////////////         {-- Warehouses --}}          ///////////////////
    public function warehouses(): Factory|View|Application
    {
        $warehouses = Warehouse::orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.warehouses', compact('warehouses'));
    }

    public function add_warehouses(): Factory|View|Application
    {
        return view('admin.warehouse-add');
    }

    public function warehouse_store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|unique:warehouses,location,'.$request->id,
            'contact_info' => 'required',
        ]);

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'location' => $request->location,
            'contact_info' => $request->contact_info,
        ]);

        return redirect()->route('admin.warehouses')->with('status', 'Warehouse has been added successfully');
    }

    public function edit_warehouse($id): Factory|View|Application
    {
        $warehouse = Warehouse::find($id);

        return view('admin.warehouse-edit', compact('warehouse'));
    }

    public function warehouse_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|unique:warehouses,location,'.$request->id,
            'contact_info' => 'required',
        ]);

        $warehouse = Warehouse::find($request->id)
            -> update([
                'name' => $request->name,
                'location' => $request->location,
                'contact_info' => $request->contact_info,
            ]);

        return redirect()->route('admin.warehouses')->with('status', 'Warehouse has been updated successfully');
    }

    public function delete_warehouse($id): RedirectResponse
    {
        Warehouse::find($id)
        -> delete();

        return redirect()->route('admin.warehouses')->with('status', 'Warehouse has benn deleted successfully');
    }


    ////////////         {-- Inventories --}}          ///////////////////
    public function inventories(): Factory|View|Application
    {
        $inventories = Inventory::orderBy('id', 'desc')
            -> paginate(10);

        return view('admin.inventories', compact('inventories'));
    }

    public function add_inventories(): Factory|View|Application
    {
        $products = Product::select('id', 'name')
            -> orderBy('name')
            -> get();

        $warehouses = Warehouse::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.inventory-add', compact('products', 'warehouses'));
    }

    public function inventory_store(Request $request): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required',
            'reorder_level' => 'required',
            'last_reorder_date' => 'required|date_format:Y-m-d H:i',
            'product_id',
            'warehouse_id',
        ]);

        $inventory = Inventory::create([
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'last_reorder_date' => $request->last_reorder_date,
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
        ]);

        return redirect()->route('admin.inventories')->with('status', 'Inventory has been added successfully');
    }

    public function edit_inventory($id): Factory|View|Application
    {
        $inventory = Inventory::find($id);

        $products = Product::select('id', 'name')
            -> orderBy('name')
            -> get();

        $warehouses = Warehouse::select('id', 'name')
            -> orderBy('name')
            -> get();

        return view('admin.inventory-edit', compact('inventory', 'products', 'warehouses'));
    }

    public function inventory_update(Request $request): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required',
            'reorder_level' => 'required',
            'last_reorder_date' => 'required|date_format:Y-m-d H:i',
            'product_id',
            'warehouse_id',
        ]);

        $inventory = Inventory::find($request->id)
            -> update([
                'quantity' => $request->quantity,
                'reorder_level' => $request->reorder_level,
                'last_reorder_date' => $request->last_reorder_date,
                'product_id' => isset($request->product_id) ? $request->product_id : 0,
                'warehouse_id' => isset($request->warehouse_id) ? $request->warehouse_id : 0,
            ]);

        return redirect()->route('admin.inventories')->with('status', 'Inventory has been updated successfully');
    }

    public function delete_inventory($id): RedirectResponse
    {
        Inventory::find($id)
            -> delete();

        return redirect()->route('admin.inventories')->with('status', 'Inventory has been deleted successfully');
    }


    ////////////         {-- Coupon --}}          ///////////////////
    public function coupons(): Factory|View|Application
    {
        $coupons = Coupon::orderBy('expiry_date', 'desc')
            -> paginate(12);

        return view('admin.coupons', compact('coupons'));
    }

    public function add_coupon(): Factory|View|Application
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'cart_value' => $request->cart_value,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function edit_coupon($id): Factory|View|Application
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function coupon_update(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = Coupon::find($request->id)
            -> update([
                'code' => $request->code,
                'type' => $request->type,
                'value' => $request->value,
                'cart_value' => $request->cart_value,
                'expiry_date' => $request->expiry_date,
            ]);

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function delete_coupon($id): RedirectResponse
    {
        Coupon::find($id)
            -> delete();

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfull!');
    }




    ////////////         {-- Order --}}          ///////////////////
    public function orders(): Factory|View|Application
    {
        $orders = Order::orderBy('created_at', 'desc')
            -> paginate(12);

        return view('admin.orders', compact('orders'));
    }

    public function order_details($id): Factory|View|Application
    {
        $order = Order::find($id);
        $orderItems = OrderItem::where('order_id', $id)
            -> orderBy('id')
            -> paginate(12);
        $transaction = Transaction::where('order_id', $id)
            -> first();

        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function update_order_status(Request $request): RedirectResponse
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;

        if($request->order_status == 'delivered')
        {
            $order->delivered_date = Carbon::now();
            $transaction = Transaction::where('order_id', $order->id)
                -> first();

            $transaction->status = 'approved';
            $transaction
                -> save();
        }
        elseif($request->order_status == 'canceled')
            $order->canceled_date = Carbon::now();

        $order
            -> save();

        return back()->with('status', 'Status changes successfully!');
    }


    ////////////         {-- Earning --}}          ///////////////////
    public function get_earning_data(Request $request)
    {
        $period = $request->query('period');

        // Fetch data based on the period
        // This is a placeholder, you'll need to implement the actual data fetching logic
        $data = [
            'this_week' => [
                'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'revenue' => [1000, 1200, 1500, 1800, 2000, 2200, 2500]
            ],
            'last_week' => [
                'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'revenue' => [900, 1100, 1400, 1700, 1900, 2100, 2400]
            ]
            // Add more periods as needed
        ];

        return response()->json($data[$period] ?? []);
    }


    ////////////         {-- Users --}}          ///////////////////
    public function users(): Factory|View|Application
    {
        $users = User::orderBy('id', 'desc')
            -> paginate(10);

        return view('admin.users', compact('users'));
    }

    public function edit_user($id): Factory|View|Application
    {
        $user = User::find($id);

        return view('admin.user-edit', compact('user'));
    }

    public function user_update(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => 'required'
        ]);

        $user = User::find($request->id);

        $user->role = $request->role;
        $user
            -> save();

        return redirect()->route('admin.users')->with('status', "User $user->name role has been updated successfully!");
    }


    ////////////         {-- Settings --}}          ///////////////////
    public function settings(): Factory|View|Application
    {
        $user = User::find(1);

        return view('admin.settings', compact('user'));
    }

    public function settings_store(Request $request): RedirectResponse
    {
        $user = User::find(1);

        if(isset($request->new_password) && $request->new_password == $request->new_password_confirmation)
        {
            if(Hash::check($request->password, $user->password))
            {
                $request->validate([
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required',
                    'old_password' => 'required',
                    'new_password' => 'required|min:8',
                    'new_password_confirmation' => 'required|min:8',
                    'image' => 'mimes:png,jpg,jpeg|max:4096'
                ]);

                if ($request->hasFile('image'))
                    if (File::exists(public_path('uploads/avatars').'/'.$user->profile_photo))
                        File::delete(public_path('uploads/avatars').'/'.$user->profile_photo);

                $image = $request->file('image');
                $file_extension = $image->extension();
                $file_name = Carbon::now()->timestamp.'.'.$file_extension;
                $this->GenerateAvatarThumbnailsImage($image, $file_name);

                $user->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone,
                    'email' => $request->email,
                    'password' => Hash::make($request->new_password),
                    'profile_image' => $file_name,
                ]);

                return back()->with('status', 'Profile has been updated successfully!');
            }
            else
                return back()->with('error', 'Wrong password');
        }
        elseif(Hash::check($request->old_password, $user->password))
        {
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'old_password' => 'required',
                'image' => 'mimes:png,jpg,jpeg|max:4096'
            ]);

            if ($request->hasFile('image'))
                if (File::exists(public_path('uploads/avatars').'/'.$user->profile_photo))
                    File::delete(public_path('uploads/avatars').'/'.$user->profile_photo);

            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateAvatarThumbnailsImage($image, $file_name);

            $user->update([
                'name' => $request->name,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'profile_image' => $file_name,
            ]);

            return back()->with('status', 'Profile has been updated successfully!');
        }
        elseif(!Hash::check($request->old_password, $user->password))
            return back()->with('error', 'Wrong password');
        else
            return back()->with('error', 'Passwords did not match!');
    }


    ////////////         {-- Slider --}}          ///////////////////
    public function slider(): Factory|View|Application
    {
        return view('admin.slider');
    }


    ////////////         {-- Images --}}          ///////////////////
    public function GenerateAvatarThumbnailsImage($image, $imageName): void
    {
        $destinationPath = public_path('upload/avatars');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function GenerateBrandThumbnailsImage($image, $imageName): void
    {
        $destinationPath = public_path('upload/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName): void
    {
        $destinationPath = public_path('upload/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function GenerateProductThumbnailsImage($image, $imageName): void
    {
        $destinationPathThumbnails = public_path('upload/products/thumbnails');
        $destinationPath = public_path('upload/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function($constraint){
            $constraint->aspectRatio();
        })
            -> save($destinationPath.'/'.$imageName);

        $img->resize(104, 104, function($constraint){
            $constraint->aspectRatio();
        })
            -> save($destinationPathThumbnails.'/'.$imageName);
    }
}

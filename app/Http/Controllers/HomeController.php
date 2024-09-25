<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $c = __DIR__.'/../config/cart.php';
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        $products = Product::orderBy('created_at', 'desc')
            -> paginate(10)
            -> take(2);

        $featuredProducts = Product::where('is_new_arrival', true)
            -> where('is_active', false)
            -> orderBy('created_at', 'desc')
            -> paginate(8);

        $hotDeals = Product::where('sales_price', ">", 0)
            -> orderBy('created_at', 'desc')
            -> paginate(5);

        $categories = Category::orderBy('created_at', 'desc')
            -> paginate(12);

        return view('to.index', compact('featuredProducts', 'hotDeals', 'categories', 'products'));
    }

    public function new_arrival(Request $request): Factory|View|Application

    {
        $size = $request->query('size') ? $request->query('size') : 12;

        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');

        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 500;

        $o_column = '';
        $o_order = '';
        $order = $request->query('order') ? $request->query('order') : -1;
        switch ($order)
        {
            case 1:
                $o_column = 'created_at';
                $o_order = 'desc';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'asc';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order = 'asc';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order = 'desc';
                break;
            default:
                $o_column = 'id';
                $o_order = 'desc';
        }
        $brands = Brand::orderBy('name', 'asc')
            -> get();

        $categories = Category::orderBy('name', 'asc')
            -> get();

        $products = Product::where(function ($query) use($f_brands){
            $query
                -> whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'".$f_brands."'=''");
        })
            -> where(function ($query) use($f_categories){
                $query
                    -> whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'".$f_categories."'=''");
            })
            -> where(function ($query) use($min_price, $max_price){
                $query
                    -> whereBetween('sales_price', [$min_price, $max_price])
                    -> orWhereBetween('regular_price', [$min_price, $max_price]);
            })
            -> orderBy($o_column, $o_order)
            -> paginate($size);

        return view('to.new-arrival', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function nice()
    {

    }
}

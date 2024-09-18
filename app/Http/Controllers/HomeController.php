<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        return view('to.index');
    }

    public function nice()
    {
        $orders = Order::orderBy('created_at', 'desc')
            -> paginate(10);

        $delivered_sum = Order::where('status', 'delivered')
            -> sum('total');

        $canceled_sum = Order::where('status', 'canceled')
            -> sum('total');

        return $delivered_sum;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{
    public function index(): Factory|View|Application
    {
        $items = Cart::instance('wishlist')->content();
        return view('to.wishlist-index', compact('items'));
    }
    public function add_to_wishlist(Request $request): RedirectResponse
    {
        Cart::instance('wishlist')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');

        return redirect()->back();
    }

    public function remove_item($rowId): RedirectResponse
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    public function empty_wishlist(): RedirectResponse
    {
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }

    public function move_to_cart($rowId): RedirectResponse
    {
        $item = Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)->associate('App\Models\Product');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;

class CartController extends Controller
{
    public function index(): Factory|View|Application
    {
        $items = Cart::instance('cart')->content();

        return view('cart.cart', compact('items'));
    }

    public function add_to_cart(Request $request): RedirectResponse
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');

        return redirect()->back();
    }

    public function increase_cart_quantity($rowId): RedirectResponse
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;

        Cart::instance('cart')->update($rowId, $qty);

        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId): RedirectResponse
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;

        Cart::instance('cart')->update($rowId, $qty);

        return redirect()->back();
    }

    public function remove_item($rowId): RedirectResponse
    {
        Cart::instance('cart')->remove($rowId);

        return redirect()->back();
    }

    public function empty_cart(): RedirectResponse
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request): RedirectResponse
    {
        $coupon_code = $request->coupon_code;

        if(isset($coupon_code))
        {
            $coupon = Coupon::where('code', $coupon_code)
                -> where('expiry_date', '>=', Carbon::today())
                -> where('cart_value', '<=', (Str::numbers(Cart::instance('cart')->subtotal()) / 100))
                -> first();

            if(!$coupon)
            {
                return redirect()->back()->with('error', 'Invalid coupon code!');
            }
            else
            {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);

                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon has been applied!');
            }
        }
        else
            return redirect()->back()->with('error', 'Invalide coupon code!');
    }

    public function calculateDiscount(): void
    {
        $discount = 0;
        if(Session::has('coupon'))
        {
            if(Session::get('coupon')['type'] == 'fixed')
            {
                $discount = Session::get('coupon')['value'];
            }
            else
            {
                $discount = ((Str::numbers(Cart::instance('cart')->subtotal()) / 100) * Session::get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = (Str::numbers(Cart::instance('cart')->subtotal()) / 100) - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);
        }
    }

    public function remove_coupon_code(): RedirectResponse
    {
        Session::forget('coupon');
        Session::forget('discount');
        return redirect()->back()->with('success', 'Coupon has been removed!');
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////


    public function checkout(): Application|Factory|View|RedirectResponse
    {
        if(!Auth::check())
            return redirect()->route('login');

        $address = Address::where('user_id', Auth::user()->id)
            -> where('is_default', true)
            -> first();

        return view('cart.checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)
            -> where('is_default', true)
            -> first();

        if(!$address)
        {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
            ]);

            $address = Address::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'city' => $request->city,
                'address' => $request->address,
                'locality' => $request->locality,
                'country' => 'Syria',
                'user_id' => $user_id,
                'is_default' => true,
            ]);
        }

        $this->setAmountForCheckout();

        $total = floatval(Str::numbers(Session::get('checkout')['total']) / 100);

        $order = Order::create([
            'user_id' => $user_id,
            'discount' => Session::get('checkout')['discount'],
            'subtotal' => Session::get('checkout')['subtotal'],
            'tax' => Session::get('checkout')['tax'],
            'total' => $total,
            'name' => $address->name,
            'phone' => $address->phone,
            'city' => $address->city,
            'address' => $address->address,
            'locality' => $address->locality,
            'country' => $address->country,
        ]);

        foreach (Cart::instance('cart')->content() as $item)
        {
            $orderItem = OrderItem::create([
                'product_id' => $item->id,
                'order_id' => $order->id,
                'price' => $item->price,
                'quantity' => $item->qty
            ]);
        }

        if($request->mode == 'card')
        {
            //
        }
        elseif ($request->mode == 'paypal')
        {
            //
        }
        else
        {
            $transaction = Transaction::create([
                'user_id' => $user_id,
                'order_id' => $order->id,
                'mode' => $request->mode,
                'status' => 'pending',
            ]);
        }

        Cart::instance('cart')->destroy();
        Session::forget('coupon');
        Session::forget('checkout');
        Session::forget('discounts');
        Session::put('order_id', $order->id);



        return redirect()->route('cart.order.confirmation');
    }

    public function setAmountForCheckout(): void
    {
        if(!Cart::instance('cart')->content()->count() > 0)
        {
            Session::forget('checkout');
            return;
        }

        if(Session::has('coupon'))
        {
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
            ]);
        }
        else
        {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
            ]);
        }
    }

    public function order_confirmation(): Application|Factory|View|RedirectResponse
    {
        if(Session::has('order_id'))
        {
            $order = Order::find(Session::get('order_id'));
            return view('cart.order-confirmation', compact('order'));
        }

        return redirect()->route('cart.index');
    }
}

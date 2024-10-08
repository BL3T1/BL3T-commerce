<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('user.index');
    }

    public function orders(): Factory|View|Application
    {
        $orders = Order::where('user_id', Auth::user()->id)
            -> orderBy('created_at', 'desc')
            -> paginate(10);

        return view('user.orders', compact('orders'));
    }

    public function order_details($id): Application|View|Factory|RedirectResponse
    {
        $order = Order::where('user_id', Auth::user()->id)
            -> where('id', $id)
            -> first();
        if($order)
        {
            $orderItems = OrderItem::where('order_id', $order->id)
                -> orderBy('id', 'desc')
                -> paginate(10);
            $transaction = Transaction::where('user_id', Auth::user()->id)
                -> first();

            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        }
        else
            return redirect()->route('login');
    }

    public function order_cancel(Request $request): RedirectResponse
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order
            -> save();
        return back()->with('success', 'Order canceled successfully!');
    }

    public function category($id): Factory|View|Application
    {
        $categroy = Category::find($id);

        return view('user.category', compact('categroy'));
    }

    public function account_details()
    {
        return view('user.account-details');
    }

    public function user_update(Request $request)
    {
        $user = User::find($request->id);

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
}

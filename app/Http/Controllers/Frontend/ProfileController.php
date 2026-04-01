<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('orderItems.product')->latest()->get();

        return view('frontend.profile.index', compact('user', 'orders'));
    }

    public function edit()
    {
        return view('frontend.profile.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function orderHistory()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('orderItems.product')->latest()->get();

        return view('frontend.profile.orders', compact('orders'));
    }
}
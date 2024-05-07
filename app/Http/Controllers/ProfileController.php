<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Client;
Use App\Models\Order;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalProfitClosure = function ($order) {
            return $order->amount * ($order->product->sell - $order->product->buy);
        };

        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalOrders = Order::count();
        $totalProfit = Order::with('product')->get()->map($totalProfitClosure)->sum();

        return view('profile', compact('user'),
        [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]
        );
    }
  
    public function edit(string $id)
    {
        $user = User::find(Auth::user()->id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:25',
            'organization' => 'nullable|string|max:25',
            'phone' => 'nullable|regex:/^\+?\d{1,3}(?:\s\d{1,3})?\d{7,12}$/',
            'photo' => 'nullable|image|mimes:jpg,png,svg,gif,jpeg|max:2048',
            'password' => 'nullable|min:8',
            'newpassword' => 'nullable|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find(Auth::user()->id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->organization = $request->input('organization');
        $user->phone = $request->input('phone');

        $currentPassword = $request->input('password');
        $newPassword = $request->input('newpassword');

        if (!empty($newPassword) && Hash::check($currentPassword, $user->password)) {
            $user->password = Hash::make($newPassword);
            $user->password = $newPassword;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);
            if ($user->photo) {
                Storage::delete('public/images/' . $user->photo);
            }
            $user->photo = $fileName;
        }
        $user->save();

        return response()->json(['status' => 200,'photo' => $fileName]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    public function index(){

        $user = User::orderBy('id', 'desc')->get();

        return view('auth.register',['data'=>$user]);

    }
    
    public function store(Request $requset){

        $user = new user();
        $user->name = $requset->name;
        $user->email = $requset->email;
        $user->password = Hash::make($requset->password);
        $user->save();

        session()->flash('success', 'You have been successfully registered!');

        return redirect()->route('register');

    }
}

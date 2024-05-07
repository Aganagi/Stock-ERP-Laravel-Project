<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    public function index(){

        $users = User::orderBy('id', 'desc')->get();

        return view('auth.login', ['data' => $users]);
    }

    public function logIn(LoginRequest $request){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            return redirect()->route('brands');

        }else{

            return redirect()->route('/')->withErrors(['message' => 'Login or password is wrong!']);
        }

    }

    public function logOut(){

        Auth()->logout();
        return redirect()->route('/');
    }
}

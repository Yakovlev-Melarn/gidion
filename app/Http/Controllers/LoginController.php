<?php

namespace App\Http\Controllers;

use App\Models\ReactSession;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('auth')) {
            return abort(404);
        }
        return view('Login/index');
    }

    public function auth(Request $request)
    {
        if (is_null($url = $request->session()->get('backPage'))) {
            $url = '/';
        }
        if (!$user = User::where('name', $request->login)->where('password', $request->password)->first()) {
            $request->session()->flash('loginStatus', 'error');
            return redirect()->back();
        }
        $request->session()->put('auth', $user->id);
        $request->session()->put('token', $user->token);
        $request->session()->forget('backPage');
        return redirect($url);
    }

    public function logout(Request $request)
    {
        ReactSession::where('userId', session('auth'))->delete();
        $request->session()->forget(['auth', 'sellerId', 'sellerName']);
        return redirect()->back();
    }
}

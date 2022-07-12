<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function login_page()
    {
        return view('admin/login');
    }

    public function login(Request $request)
    {
        config()->set('auth.defaults.guard', 'admin');

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $credential = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credential)) {
            Auth::guard('admin')->attempt($credential, $request->filled('remember'));
            return redirect()->intended(route('admin.home'));
        }

        return redirect()->back()->withInput($request->only('username', 'password'))->withErrors(['error' => ['Username atau password salah!']]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return redirect('/admin');
    }
}

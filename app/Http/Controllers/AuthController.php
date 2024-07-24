<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('Auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'password' => 'required',
        ]);

        $employee = User::where('username', $request->nik)->first();

        if($employee && $request -> password == '123456.')
        {
            Auth::login($employee);
            if (Auth::check()) {
                // return redirect()->intended(route('index'));
                return redirect()->intended(route('dashboard'));
            }
        } 
        else
        {
            return back()->withErrors(['error' => 'NIK atau password salah.'])->withInput();
        }
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

}

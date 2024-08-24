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
                if (Auth::user()->kode_bagian == 'k45' || Auth::user()->kode_bagian == 'os26') {
                    return redirect()->intended(route('dashboard'));
                }
                elseif (Auth::user()->kode_bagian == 'k2' || Auth::user()->kode_bagian == 'os15') {
                    return redirect() -> intended(route('keuangan'));
                }
                elseif (Auth::user()->kode_bagian == 'k32') {
                    return redirect() -> route('hk');
                }
                elseif (Auth::user()->kode_bagian == 'k13' || Auth::user()->kode_bagian == 'k14' || Auth::user()->kode_bagian == 'k15' || Auth::user()->kode_bagian == 'k16') {
                    return redirect() -> route('ranap');
                }
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

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $employee = Employee::where('username', $request->nik)->first();

        if($employee && $request -> password == '123456.')
        {
            Auth::login($employee);
            if (Auth::check()) {
                switch (Auth::user()->kode_bagian) {
                    case 'k45' || 'os26' || 'k98': // IT, DIREKSI
                        return redirect()->intended(route('dashboard'));
                    case 'k2' || 'k67': // Keuangan
                        return redirect()->intended(route('keuangan'));
                    case 'k32': // Housekeeping
                        return redirect()->route('hk');
                    case 'k13':
                    case 'k14':
                    case 'k15':
                    case 'k16': 
                    case 'k41': 
                    case 'k58': 
                    case 'k59': // Inpatient (Ranap)
                        return redirect()->route('ranap');
                    default:
                        Auth::logout();
                        return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
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

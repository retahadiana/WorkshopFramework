<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();
        $authenticated = false;

        if ($user) {
            try {
                $authenticated = Hash::check($data['password'], $user->password);
            } catch (\RuntimeException $e) {
                $stored = $user->password;
                if ($stored === $data['password']) {
                    $authenticated = true;
                } elseif (is_string($stored) && strlen($stored) === 32 && md5($data['password']) === $stored) {
                    $authenticated = true;
                }

                if ($authenticated) {
                    $user->password = Hash::make($data['password']);
                    $user->save();
                }
            }
        }

        if ($authenticated) {
            Auth::login($user);
            session(['user' => $user->only('id', 'name', 'email')]);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

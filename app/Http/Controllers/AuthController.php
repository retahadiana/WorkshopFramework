<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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
            return $this->beginOtpChallenge($request, $user);
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('pending_otp_user_id');
        $request->session()->forget('debug_otp_code');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Redirect ke Google untuk authentikasi
     */
    public function redirectToGoogle(): RedirectResponse
    {
        config(['services.google.redirect' => $this->resolveGoogleRedirectUrl()]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            config(['services.google.redirect' => $this->resolveGoogleRedirectUrl()]);

            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'host' => $request->getHost(),
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed. Check Google redirect URL/domain and try again.']);
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google account does not provide an email.']);
        }

        // Cari user berdasarkan id_google atau email
        $user = User::where('id_google', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            // User baru: create account dari Google
            $user = User::create([
                'name' => $googleUser->getName() ?: explode('@', $email)[0],
                'email' => $email,
                'id_google' => $googleId,
                'password' => Hash::make(Str::random(32)),
            ]);
        } elseif (! $user->id_google) {
            // User existing, link ke Google ID
            $user->id_google = $googleId;
            $user->save();
        }

        // Lanjut ke OTP challenge
        return $this->beginOtpChallenge($request, $user);
    }

    /**
     * Tampilkan form OTP
     */
    public function showOtpForm(Request $request)
    {
        if (! $request->session()->has('pending_otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Verifikasi OTP yang diinputkan user
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $pendingUserId = $request->session()->get('pending_otp_user_id');
        if (! $pendingUserId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'OTP session expired, please login again.']);
        }

        $user = User::find($pendingUserId);
        if (! $user) {
            $request->session()->forget('pending_otp_user_id');
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found. Please login again.']);
        }

        // Validasi OTP (case-insensitive)
        if (strtoupper($data['otp']) !== strtoupper((string) $user->otp)) {
            return back()->withErrors(['otp' => 'OTP code is invalid.'])
                ->onlyInput('otp');
        }

        // OTP valid: clear OTP code, login user, create session
        $user->otp = null;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();
        session(['user' => $user->only('id', 'name', 'email')]);
        $request->session()->forget('pending_otp_user_id');

        return redirect('/dashboard');
    }

    /**
     * Generate OTP, kirim email, simpan session
     */
    private function beginOtpChallenge(Request $request, User $user): RedirectResponse
    {
        // Generate 6 karakter OTP random (uppercase)
        $otpCode = strtoupper(Str::random(6));
        
        // Simpan ke database user
        $user->otp = $otpCode;
        $user->save();

        // Store user ID di session untuk pending OTP verification
        $request->session()->put('pending_otp_user_id', $user->id);

        // Kirim OTP via email
        try {
            Mail::raw("Kode OTP login Anda: {$otpCode}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode OTP Login');
            });
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            $request->session()->forget('pending_otp_user_id');

            return redirect()->route('login')
                ->withErrors(['email' => 'Failed to send OTP email. Gunakan Gmail App Password (bukan password akun Gmail biasa).']);
        }

        return redirect()->route('otp.form');
    }

    private function resolveGoogleRedirectUrl(): string
    {
        return url('/auth/google/callback');
    }
}

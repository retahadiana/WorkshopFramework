<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koleksi Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-50 via-purple-50 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-lg mb-6">
                <i class="fas fa-book text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Welcome Back</h1>
            <p class="text-gray-600 text-base">Sign in to access your book collection</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 mb-6">
            <!-- Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-800 mb-2">Login Failed</h3>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Email/Password Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-base font-semibold text-gray-800 mb-2">
                        <i class="fas fa-envelope text-purple-600 mr-2"></i>Email Address
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-100 transition @error('email') border-red-500 @enderror"
                        required
                    >
                    @error('email')
                        <p class="text-sm text-red-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-base font-semibold text-gray-800 mb-2">
                        <i class="fas fa-lock text-purple-600 mr-2"></i>Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Enter your password"
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-100 transition @error('password') border-red-500 @enderror"
                        required
                    >
                    @error('password')
                        <p class="text-sm text-red-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember"
                            class="w-5 h-5 rounded-lg border-2 border-gray-300 text-purple-600 focus:ring-2 focus:ring-purple-500 cursor-pointer"
                        >
                        <span class="text-sm font-medium text-gray-700">Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-semibold text-purple-600 hover:text-purple-700 hover:underline transition">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-lg font-bold rounded-xl hover:from-purple-700 hover:to-purple-800 active:from-purple-800 active:to-purple-900 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-8">
                <div class="h-px bg-gray-300 flex-1"></div>
                <span class="text-sm font-medium text-gray-600">or continue with</span>
                <div class="h-px bg-gray-300 flex-1"></div>
            </div>

            <!-- Google OAuth Button -->
            <a 
                href="{{ route('auth.google.redirect') }}"
                class="w-full py-4 bg-white border-2 border-gray-300 text-gray-800 text-lg font-semibold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition flex items-center justify-center gap-4 shadow hover:shadow-md"
            >
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Sign in with Google</span>
            </a>
        </div>

        <!-- Footer Links -->
        <div class="text-center text-sm text-gray-700">
            <p>
                Don't have an account?
                <a href="#" class="font-bold text-purple-600 hover:text-purple-700 hover:underline transition">
                    Create one
                </a>
            </p>
        </div>

        <!-- Security Info -->
        <div class="mt-8 text-center text-xs text-gray-600">
            <p class="flex items-center justify-center gap-2">
                <i class="fas fa-shield-alt text-green-600"></i>
                Your data is secure and encrypted
            </p>
        </div>
    </div>

    <script>
        // Password visibility toggle (optional enhancement)
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.querySelector('[data-toggle-password]');
        
        if(togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });
        }

        // Add input focus animations
        document.querySelectorAll('input[type="email"], input[type="password"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>

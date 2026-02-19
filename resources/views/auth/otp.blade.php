<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Koleksi Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-50 via-purple-50 to-purple-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-purple-600 to-purple-700 shadow-lg mb-6">
                <i class="fas fa-shield-alt text-white text-4xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Verifikasi OTP</h1>
            <p class="text-gray-600 text-base">Masukkan kode 6 karakter yang dikirim ke email Anda</p>
        </div>

        <!-- OTP Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 mb-6">
            <!-- Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-800 mb-2">Verifikasi Gagal</h3>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-times-circle mr-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
                @csrf

                <!-- OTP Input Field -->
                <div>
                    <label for="otp" class="block text-base font-semibold text-gray-800 mb-3">
                        <i class="fas fa-key text-purple-600 mr-2"></i>Kode OTP
                    </label>
                    <input
                        type="text"
                        name="otp"
                        id="otp"
                        maxlength="6"
                        placeholder="ABC123"
                        class="w-full px-6 py-6 text-4xl font-mono font-bold tracking-[0.5rem] border-3 border-gray-300 rounded-xl focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-100 transition text-center uppercase"
                        required
                    >
                    @error('otp')
                        <p class="text-sm text-red-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Info Text -->
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-sm text-purple-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        Kode OTP berlaku selama 10 menit. Pastikan Anda memasukkan kode dengan benar.
                    </p>
                </div>

                <!-- Verify Button -->
                <button
                    type="submit"
                    class="w-full py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-lg font-bold rounded-xl hover:from-purple-700 hover:to-purple-800 active:from-purple-800 active:to-purple-900 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    <i class="fas fa-check-circle mr-2"></i>Verifikasi Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-8">
                <div class="h-px bg-gray-300 flex-1"></div>
                <span class="text-sm font-medium text-gray-600">atau</span>
                <div class="h-px bg-gray-300 flex-1"></div>
            </div>

            <!-- Resend OTP Section -->
            <div class="text-center border-t-2 border-gray-200 pt-8">
                <p class="text-gray-700 mb-4">
                    Tidak menerima kode?
                    <a href="{{ route('login') }}" class="text-purple-600 font-semibold hover:text-purple-700 transition">
                        Coba lagi
                    </a>
                </p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-purple-600 transition font-medium">
                    <i class="fas fa-arrow-left"></i>Kembali ke Halaman Login
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center text-xs text-gray-600">
            <p class="flex items-center justify-center gap-2">
                <i class="fas fa-lock text-purple-600"></i>
                Kode OTP Anda dijaga dengan aman
            </p>
        </div>
    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            
            if (otpInput) {
                // Focus otomatis saat page load
                otpInput.focus();
                
                // Auto-format input - hanya izinkan alphanumeric (huruf dan angka)
                otpInput.addEventListener('input', function(e) {
                    // Hanya izinkan A-Z dan 0-9
                    this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 6);
                });
            }
        });
    </script>

    <style>
        .letter-spacing {
            letter-spacing: 0.5rem;
        }
    </style>
</body>
</html>

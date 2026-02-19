<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku - Manage Your Book Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center">
                        <i class="fas fa-book text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Koleksi Buku</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-gray-700 hover:text-purple-600 transition font-medium">Features</a>
                    <a href="#about" class="text-gray-700 hover:text-purple-600 transition font-medium">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-purple-600 transition font-medium">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2 text-purple-600 border-2 border-purple-600 rounded-lg hover:bg-purple-50 transition font-semibold">
                            Sign In
                        </a>
                        <a href="#" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold hidden sm:block">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-white to-purple-50 pt-20 pb-32">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-5xl lg:text-6xl font-black text-gray-900 mb-6 leading-tight">
                        Manage Your
                        <span class="bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">Book Collection</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Organize, track, and manage your personal book library. Discover new books, categorize your collection, and keep all your reading information in one place.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-bold text-lg shadow-lg hover:shadow-xl">
                                <i class="fas fa-sign-in-alt mr-2"></i>Get Started
                            </a>
                            <a href="#features" class="px-8 py-4 bg-white border-2 border-gray-300 text-gray-900 rounded-lg hover:bg-gray-50 transition font-bold text-lg">
                                <i class="fas fa-arrow-down mr-2"></i>Learn More
                            </a>
                        @else
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-bold text-lg shadow-lg hover:shadow-xl">
                                <i class="fas fa-home mr-2"></i>Go to Dashboard
                            </a>
                        @endguest
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 flex justify-center lg:justify-start gap-8">
                        <div>
                            <p class="text-3xl font-bold text-purple-600">1000+</p>
                            <p class="text-gray-600 font-medium">Books Tracked</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-purple-600">500+</p>
                            <p class="text-gray-600 font-medium">Active Users</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-purple-600">50+</p>
                            <p class="text-gray-600 font-medium">Categories</p>
                        </div>
                    </div>
                </div>

                <!-- Right Illustration -->
                <div class="relative hidden lg:block">
                    <div class="w-full aspect-square rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center shadow-2xl">
                        <div class="text-center">
                            <i class="fas fa-books text-purple-600" style="font-size: 150px;"></i>
                            <p class="text-purple-600 font-semibold mt-4">Your Library Awaits</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600">Everything you need to manage your book collection efficiently</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Book Management</h3>
                    <p class="text-gray-600">Add, edit, and organize your books with detailed information including title, author, and ISBN.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Categorization</h3>
                    <p class="text-gray-600">Organize your collection with custom categories. Fiction, Non-fiction, Mystery, and more.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Quick Search</h3>
                    <p class="text-gray-600">Find any book instantly with our powerful search and filtering capabilities.</p>
                </div>

                <!-- Feature 4 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Statistics</h3>
                    <p class="text-gray-600">View insights about your collection including total books, genres, and reading progress.</p>
                </div>

                <!-- Feature 5 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Mobile Friendly</h3>
                    <p class="text-gray-600">Access your library from anywhere, anytime on any device with full responsiveness.</p>
                </div>

                <!-- Feature 6 -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 hover:shadow-lg transition">
                    <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure & Private</h3>
                    <p class="text-gray-600">Your data is encrypted and secure. Only you have access to your personal library.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-purple-600 to-purple-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Organize Your Library?</h2>
            <p class="text-xl text-purple-100 mb-8">Join hundreds of book lovers managing their collections with Koleksi Buku.</p>

            @guest
                <a href="{{ route('login') }}" class="inline-block px-8 py-4 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-rocket mr-2"></i>Start Your Journey
                </a>
            @else
                <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-4 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>Go to Dashboard
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Koleksi Buku</span>
                    </div>
                    <p class="text-sm">Manage your book collection with ease.</p>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-purple-400 transition">Features</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Security</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#about" class="hover:text-purple-400 transition">About</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Blog</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Careers</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-purple-400 transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Terms</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-400">&copy; 2026 Koleksi Buku. All rights reserved.</p>
                    <div class="flex gap-4 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        @keyframes blob {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</body>
</html>

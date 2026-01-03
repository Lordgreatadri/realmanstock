<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Realman Livestock - Premium Livestock & Quality Groceries</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    <script>
        // Auto-dismiss success/error messages after 8 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[class*="fixed top-20"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => alert.remove(), 500);
                }, 8000);
            });
        });
    </script>
</head>
<body class="bg-gray-950 text-gray-100 antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-gray-950/80 backdrop-blur-lg border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">R</span>
                    </div>
                    <span class="text-xl font-bold text-white">Realman Livestock</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="#products" class="text-gray-300 hover:text-white transition">Products</a>
                    <a href="#about" class="text-gray-300 hover:text-white transition">About</a>
                    <a href="#contact" class="text-gray-300 hover:text-white transition">Contact</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4">
            <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 text-green-400 shadow-lg animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4">
            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 text-red-400 shadow-lg animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <section id="home" class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/20 via-gray-950 to-purple-900/20"></div>
        
        <div class="relative max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 text-transparent bg-clip-text">
                    Premium Livestock &<br> Quality Groceries
                </h1>
                <p class="text-xl md:text-2xl text-gray-400 mb-8 max-w-3xl mx-auto">
                    Your trusted source for fresh, quality livestock and groceries. 
                    From farm to your table with care and excellence.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('order') }}" class="px-8 py-4 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium">
                        Browse Products
                    </a>
                    <a href="#contact" class="px-8 py-4 rounded-lg bg-gray-800 text-white font-medium hover:bg-gray-700 transition">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="products" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-900/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Our Product Categories</h2>
                <p class="text-gray-400 text-lg">Explore our wide range of quality livestock and groceries</p>
            </div>

            <!-- Livestock Categories -->
            <div class="mb-12">
                <h3 class="text-2xl font-bold mb-6 text-indigo-400">Livestock</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($livestockCategories as $category)
                    <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700 hover:border-indigo-500 transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="text-xl font-semibold text-white mb-2">{{ $category->name }}</h4>
                                <p class="text-gray-400 text-sm">{{ $category->description }}</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-400 rounded-full text-sm">
                                {{ $category->animals_count }} Available
                            </span>
                        </div>
                        <a href="{{ route('order') }}" class="inline-flex items-center text-indigo-400 hover:text-indigo-300 transition">
                            View Products →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Grocery Categories -->
            <div>
                <h3 class="text-2xl font-bold mb-6 text-purple-400">Groceries</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($groceryCategories as $category)
                    <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700 hover:border-purple-500 transition-all">
                        <h4 class="text-lg font-semibold text-white mb-2">{{ $category->name }}</h4>
                        <p class="text-gray-400 text-sm mb-4">{{ $category->description }}</p>
                        <a href="{{ route('order') }}" class="text-purple-400 hover:text-purple-300 transition text-sm">
                            Browse →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Featured Products</h2>
                <p class="text-gray-400 text-lg">Check out our premium selection</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredAnimals as $animal)
                <div class="bg-gray-800/50 rounded-xl overflow-hidden border border-gray-700 hover:border-indigo-500 transition-all">
                    <div class="aspect-video bg-gray-700 flex items-center justify-center">
                        @if($animal->image)
                            <img src="{{ asset('storage/' . $animal->image) }}" alt="{{ $animal->breed }}" class="w-full h-full object-cover">
                        @else
                            @php
                                // Determine emoji based on category name or breed
                                $categoryName = strtolower($animal->category->name ?? '');
                                $breed = strtolower($animal->breed ?? '');
                                
                                if (str_contains($categoryName, 'goat') || str_contains($breed, 'goat')) {
                                    $emoji = '🐐';
                                } elseif (str_contains($categoryName, 'sheep') || str_contains($breed, 'sheep')) {
                                    $emoji = '🐑';
                                } elseif (str_contains($categoryName, 'cow') || str_contains($categoryName, 'cattle') || str_contains($breed, 'cow')) {
                                    $emoji = '🐄';
                                } elseif (str_contains($categoryName, 'chicken') || str_contains($categoryName, 'fowl') || str_contains($categoryName, 'poultry') || str_contains($breed, 'chicken') || str_contains($breed, 'fowl')) {
                                    $emoji = '🐔';
                                } elseif (str_contains($categoryName, 'pig') || str_contains($breed, 'pig')) {
                                    $emoji = '🐷';
                                } elseif (str_contains($categoryName, 'rabbit') || str_contains($breed, 'rabbit')) {
                                    $emoji = '🐰';
                                } elseif (str_contains($categoryName, 'turkey') || str_contains($breed, 'turkey')) {
                                    $emoji = '🦃';
                                } elseif (str_contains($categoryName, 'duck') || str_contains($breed, 'duck')) {
                                    $emoji = '🦆';
                                } else {
                                    $emoji = '🐾'; // Generic livestock
                                }
                            @endphp
                            <span class="text-gray-500 text-6xl">{{ $emoji }}</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="text-xs text-indigo-400 font-medium">{{ $animal->category->name }}</span>
                        <h3 class="text-lg font-semibold text-white mt-2">{{ $animal->breed ?? $animal->category->name }}</h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-2xl font-bold text-indigo-400">
                                @if($animal->fixed_selling_price)
                                    ${{ number_format($animal->fixed_selling_price, 2) }}
                                @else
                                    ${{ number_format($animal->selling_price_per_kg ?? 0, 2) }}/kg
                                @endif
                            </span>
                            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">
                                {{ ucfirst($animal->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('order') }}" class="inline-flex items-center px-8 py-4 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium">
                    View All Products →
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-900/50">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-6">About Realman Livestock</h2>
            <p class="text-gray-400 text-lg max-w-3xl mx-auto mb-8">
                We are a trusted provider of premium quality livestock and groceries, serving our community with dedication and excellence. 
                Our commitment to quality and customer satisfaction sets us apart.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
                    <div class="text-4xl mb-4">✓</div>
                    <h4 class="font-semibold text-white mb-2">Quality Assurance</h4>
                    <p class="text-gray-400 text-sm">All our livestock are healthy and well-maintained</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
                    <div class="text-4xl mb-4">⚡</div>
                    <h4 class="font-semibold text-white mb-2">Professional Processing</h4>
                    <p class="text-gray-400 text-sm">Expert dressing and processing services</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
                    <div class="text-4xl mb-4">🚚</div>
                    <h4 class="font-semibold text-white mb-2">Flexible Delivery</h4>
                    <p class="text-gray-400 text-sm">Pickup or delivery options available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Get In Touch</h2>
                <p class="text-gray-400 text-lg">Have questions? We'd love to hear from you</p>
            </div>

            <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-lg text-green-400">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white" placeholder="John Doe">
                            @error('name')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white" placeholder="+1 234 567 8900">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white" placeholder="john@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                        <textarea name="message" rows="4" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white" placeholder="Tell us what you need...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full px-8 py-4 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium hover:from-indigo-600 hover:to-purple-700 transition">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">R</span>
                        </div>
                        <span class="text-xl font-bold text-white">Realman</span>
                    </div>
                    <p class="text-gray-400 text-sm">Premium livestock and quality groceries for your needs.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#products" class="hover:text-white transition">Products</a></li>
                        <li><a href="#about" class="hover:text-white transition">About</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>Phone: +1 234 567 8900</li>
                        <li>Email: info@realmanlivestock.com</li>
                        <li>Address: 123 Farm Road</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-500 transition">f</a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-500 transition">t</a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-indigo-500 transition">i</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                <p class="mb-2">&copy; {{ date('Y') }} Realman Livestock. All rights reserved.</p>
                <p class="text-xs mb-2">
                    Developed with 
                    <span class="inline-block animate-pulse text-red-500">❤️</span> 
                    by 
                    <a href="mailto:lordgreatadri@gmail.com" 
                       class="text-indigo-400 hover:text-indigo-300 transition-all duration-300 hover:underline font-medium">
                        Lordgreat-Adri
                    </a>
                </p>
                <div class="flex items-center justify-center space-x-4 text-xs">
                    <a href="https://github.com/Lordgreatadri" 
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center space-x-1 text-gray-400 hover:text-indigo-400 transition-all duration-300">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        <span>GitHub</span>
                    </a>
                    <span class="text-gray-600">•</span>
                    <a href="https://www.linkedin.com/in/lordgreat-adri" 
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center space-x-1 text-gray-400 hover:text-indigo-400 transition-all duration-300">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        <span>LinkedIn</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

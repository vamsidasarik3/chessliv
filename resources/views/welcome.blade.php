<x-app-layout>
    <x-slot name="title">Welcome to Chess LIV - Live Chess Platforms</x-slot>

    <div class="relative overflow-hidden bg-white">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-16 md:py-24">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-12 md:mb-0">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
                        Play Chess <br/>
                        <span class="text-blue-600">Live & Competitive</span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 max-w-lg">
                        Experience the thrill of real-time chess tournaments. Join thousands of players worldwide in the most premium chess platform ever built.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-center hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Start Playing
                        </a>
                        <a href="#" class="bg-slate-100 text-slate-700 px-8 py-4 rounded-xl font-bold text-center hover:bg-slate-200 transition">
                            View Tournaments
                        </a>
                    </div>
                    <div class="mt-10 flex items-center space-x-4 text-sm text-slate-500">
                        <div class="flex -space-x-2">
                            <img class="w-8 h-8 rounded-full border-2 border-white bg-slate-200" src="https://ui-avatars.com/api/?name=User+1&background=random" alt="">
                            <img class="w-8 h-8 rounded-full border-2 border-white bg-slate-200" src="https://ui-avatars.com/api/?name=User+2&background=random" alt="">
                            <img class="w-8 h-8 rounded-full border-2 border-white bg-slate-200" src="https://ui-avatars.com/api/?name=User+3&background=random" alt="">
                        </div>
                        <span>Join <span class="font-bold text-slate-900">10,000+</span> active grandmasters</span>
                    </div>
                </div>
                <div class="md:w-1/2 relative">
                    <div class="bg-blue-600 rounded-3xl p-2 shadow-2xl transform rotate-2">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-inner aspect-square flex items-center justify-center p-8">
                            <!-- Placeholder for a Chess Board Image or Graphic -->
                            <div class="grid grid-cols-4 grid-rows-4 w-full h-full border-4 border-slate-900">
                                @for($i = 0; $i < 16; $i++)
                                    <div class="{{ ($i + floor($i/4)) % 2 == 0 ? 'bg-white' : 'bg-slate-900' }}"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-indigo-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse delay-700"></div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="bg-slate-50 py-20">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Why Choose Chess LIV?</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Designed for competitive players who demand performance, accuracy, and a premium experience.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Ultra-Low Latency</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Our global server network ensures your moves are registered instantly, no matter where you are.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Fair Play Guaranteed</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Advanced anti-cheat systems and manual moderation to keep the competition clean and rewarding.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Detailed Analysis</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Post-game insights powered by the latest engines to help you identify blunders and improve your strategy.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

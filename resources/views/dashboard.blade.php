<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Player Dashboard') }}
            </h2>
            <div class="flex items-center space-x-2 text-sm text-slate-500">
                <span>Welcome back,</span>
                <span class="font-bold text-slate-900">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Blitz Rating</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-slate-900">1450</h3>
                        <span class="text-green-500 text-xs font-bold">+12 today</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Rapid Rating</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-slate-900">1520</h3>
                        <span class="text-slate-400 text-xs font-bold">Stable</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Win Rate</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-slate-900">54%</h3>
                        <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden mb-2">
                            <div class="bg-blue-600 h-full w-[54%]"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-600 p-6 rounded-2xl shadow-lg shadow-blue-200 text-white hover:bg-blue-700 transition cursor-pointer">
                    <p class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-1">New Game</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-bold">Start Match</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Games -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Recent Games</h3>
                            <a href="#" class="text-sm text-blue-600 font-bold hover:underline">View History</a>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-slate-100">
                                    @foreach([['Opponent' => 'GrandmasterX', 'Result' => 'Win', 'Rating' => '+8'], ['Opponent' => 'ChessQueen', 'Result' => 'Loss', 'Rating' => '-6'], ['Opponent' => 'KnightRider', 'Result' => 'Draw', 'Rating' => '0']] as $game)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                                                </div>
                                                <span class="font-bold text-slate-800">{{ $game['Opponent'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $game['Result'] === 'Win' ? 'bg-green-100 text-green-700' : ($game['Result'] === 'Loss' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700') }}">
                                                {{ strtoupper($game['Result']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold {{ str_contains($game['Rating'], '+') ? 'text-green-600' : (str_contains($game['Rating'], '-') ? 'text-red-600' : 'text-slate-400') }}">
                                            {{ $game['Rating'] }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-slate-400 text-xs">
                                            2h ago
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Puzzles / Training -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-indigo-900 rounded-2xl p-6 text-white relative overflow-hidden group cursor-pointer">
                            <div class="relative z-10">
                                <h4 class="font-bold text-xl mb-2">Daily Puzzle</h4>
                                <p class="text-indigo-200 text-sm mb-4">White to mate in 3. Solve now to earn 50 tokens.</p>
                                <span class="bg-indigo-700 px-4 py-2 rounded-lg text-sm font-bold group-hover:bg-indigo-600 transition">Solve Now</span>
                            </div>
                            <div class="absolute -right-4 -bottom-4 opacity-10 transform group-hover:scale-110 transition">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l-5.5 9h11L12 2zm0 14l5.5-9h-11L12 16z"></path></svg>
                            </div>
                        </div>
                        <div class="bg-slate-900 rounded-2xl p-6 text-white relative overflow-hidden group cursor-pointer">
                            <div class="relative z-10">
                                <h4 class="font-bold text-xl mb-2">Analysis Engine</h4>
                                <p class="text-slate-400 text-sm mb-4">Review your games with Stockfish 16.1 depth 24.</p>
                                <span class="bg-slate-800 px-4 py-2 rounded-lg text-sm font-bold group-hover:bg-slate-700 transition">Open Analysis</span>
                            </div>
                            <div class="absolute -right-4 -bottom-4 opacity-10 transform group-hover:scale-110 transition">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Content -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Active Tournaments</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black">B</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Blitz Championship</p>
                                    <p class="text-xs text-blue-600">Starting in 15m</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500 font-black">R</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Rapid Masters</p>
                                    <p class="text-xs text-slate-400">Entry: 50 Tokens</p>
                                </div>
                            </div>
                        </div>
                        <button class="w-full mt-6 bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition">View All</button>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-100">
                        <h4 class="font-black text-xl mb-1">PRO Membership</h4>
                        <p class="text-orange-100 text-sm mb-4">Unlock unlimited analysis and premium badges.</p>
                        <button class="bg-white text-orange-600 px-6 py-2 rounded-xl font-bold hover:shadow-lg transition">Upgrade Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

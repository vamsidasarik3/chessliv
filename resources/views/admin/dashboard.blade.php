@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Total Users</p>
                <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['total_users'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Admins</p>
                <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['admin_users'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Active Users</p>
                <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['active_users'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-200">
        <h3 class="font-bold text-slate-900">Recent Registrations</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @foreach($stats['recent_users'] as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <img class="w-8 h-8 rounded-full bg-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="">
                            <div>
                                <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="flex items-center {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                            <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-600' : 'bg-red-600' }} mr-2"></span>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $user->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 text-sm font-bold hover:underline">View All Users</a>
    </div>
</div>
@endsection

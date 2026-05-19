@extends('layouts.admin')

@section('page_title', 'User Details: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="flex items-center text-slate-500 hover:text-slate-700 transition font-bold text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Users
        </a>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 text-sm">
                Edit User
            </a>
            <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-white text-slate-700 border border-slate-200 px-6 py-2 rounded-xl font-bold hover:bg-slate-50 transition text-sm">
                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">
                <img class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-slate-100 bg-slate-50" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=128&background=random" alt="">
                <h3 class="text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                <p class="text-slate-500 text-sm mb-4">{{ $user->email }}</p>
                
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ strtoupper($user->role) }}
                </span>
                
                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-center space-x-2">
                    <span class="w-3 h-3 rounded-full {{ $user->is_active ? 'bg-green-600' : 'bg-red-600' }}"></span>
                    <span class="font-bold text-sm {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->is_active ? 'Active Account' : 'Deactivated' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Account Details -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="font-bold text-slate-900">Account Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Full Name</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Email Address</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Role</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ ucfirst($user->role) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Email Status</dt>
                            <dd class="mt-1 text-sm font-bold">
                                @if($user->email_verified_at)
                                    <span class="text-green-600">Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                @else
                                    <span class="text-red-500 font-bold uppercase text-xs">Unverified</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Joined Date</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ $user->created_at->format('M d, Y') }} ({{ $user->created_at->diffForHumans() }})</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 uppercase tracking-widest">Last Updated</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ $user->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-blue-400 font-bold text-xs uppercase tracking-widest mb-2">Activity Log</h4>
                    <p class="text-sm text-slate-300">Detailed user activity tracking will be available in the next update.</p>
                </div>
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

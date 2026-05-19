@extends('layouts.admin')

@section('page_title', 'Edit User: ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 rounded-xl border @error('name') border-red-500 @else border-slate-300 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 rounded-xl border @error('email') border-red-500 @else border-slate-300 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="role" class="block text-sm font-bold text-slate-700 mb-2">Role</label>
                        <select name="role" id="role" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none">
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label for="is_active" class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                        <select name="is_active" id="is_active" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end space-x-4 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="text-slate-500 font-bold hover:text-slate-700 transition">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-slate-50 p-6 rounded-2xl border border-slate-200">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Account Information</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400">Registered</p>
                <p class="font-bold text-slate-700">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-slate-400">Email Verified</p>
                <p class="font-bold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                    {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Not Verified' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

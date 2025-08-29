@extends('layouts.app')

@section('title', 'Edit Profile - NetOnYou')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-600 mt-2">Update your personal information</p>
        </div>

        <!-- Profile Edit Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Profile Information</h2>

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1d003f] focus:border-transparent">
                    </div>

                    <!-- Email (Read-only) -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-500 cursor-not-allowed">
                        <p class="text-sm text-gray-500 mt-1">Email address cannot be changed</p>
                    </div>

                    <!-- Language Preference Info -->
                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Language Preference</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>You can change your language preference using the GTranslate widget in the header. Your language choice will be automatically saved and restored on future logins.</p>
                                        <p class="mt-1">Current language: <strong>{{ strtoupper($user->language) }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Address -->
                    <div class="md:col-span-2">
                        <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-2">Wallet Address</label>
                        <input type="text" id="wallet_address" name="wallet_address" value="{{ old('wallet_address', $user->wallet_address) }}"
                               placeholder="0x..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1d003f] focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Optional - Your cryptocurrency wallet address</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-[#1d003f] text-white px-6 py-2 rounded-lg font-medium hover:bg-[#2a0057] transition-colors">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Password</h2>
                <a href="{{ route('profile.change-password') }}" class="bg-[#1d003f] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#2a0057] transition-colors">
                    Change Password
                </a>
            </div>
            <p class="text-gray-600">Keep your account secure by using a strong password and updating it regularly.</p>
        </div>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Change Password - NetOnYou')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Change Password</h1>
            <p class="text-gray-600 mt-2">Update your account password</p>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Password Change</h2>

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.change-password.update') }}">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1d003f] focus:border-transparent"
                           placeholder="Enter your current password">
                </div>

                <!-- New Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1d003f] focus:border-transparent"
                           placeholder="Enter your new password">
                    <p class="text-sm text-gray-500 mt-1">Password must be at least 8 characters long</p>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1d003f] focus:border-transparent"
                           placeholder="Confirm your new password">
                </div>

                <!-- Password Requirements -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• At least 8 characters long</li>
                        <li>• Should contain uppercase and lowercase letters</li>
                        <li>• Should contain numbers and special characters</li>
                        <li>• Should be different from your current password</li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-[#1d003f] text-white px-6 py-2 rounded-lg font-medium hover:bg-[#2a0057] transition-colors">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


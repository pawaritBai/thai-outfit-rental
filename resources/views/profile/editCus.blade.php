@extends('layouts.main')

@section('title', 'Edit Profile')

@section('content')
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    ✏️ <span class="ml-2">Edit Profile</span>
                </h2>
                <a href="{{ route('profile.index') }}" class="text-sm text-blue-500 hover:underline">
                    ← Back to Profile
                </a>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Picture -->
                    <div>
                        <label for="profilePicture" class="block font-medium text-sm text-gray-700">
                            Profile Picture
                        </label>
                        <input id="profilePicture" name="profilePicture" type="file" 
                               class="mt-1 block w-full text-sm border rounded-md p-2 bg-gray-50">
                        @error('profilePicture') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            Name
                        </label>
                        <input id="name" name="name" type="text" 
                               class="mt-1 block w-full border rounded-md p-2 bg-gray-50" 
                               value="{{ old('name', $user->name) }}" required autofocus>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-medium text-sm text-gray-700">
                            Email
                        </label>
                        <input id="email" name="email" type="email" 
                               class="mt-1 block w-full border rounded-md p-2 bg-gray-50" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Mobile Number -->
                    <div>
                        <label for="phone" class="block font-medium text-sm text-gray-700">
                            Mobile Number
                        </label>
                        <input id="phone" name="phone" type="text" 
                               class="mt-1 block w-full border rounded-md p-2 bg-gray-50" 
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700">
                            New Password
                        </label>
                        <input id="password" name="password" type="password" 
                               class="mt-1 block w-full border rounded-md p-2 bg-gray-50" 
                               autocomplete="new-password" placeholder="Leave blank if not changing">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">
                            Confirm New Password
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                               class="mt-1 block w-full border rounded-md p-2 bg-gray-50" 
                               autocomplete="new-password">
                        @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gender (Full width) -->
                    <div class="md:col-span-2">
                        <label class="block font-medium text-sm text-gray-700">
                            Gender
                        </label>
                        <div class="flex items-center space-x-6 mt-2">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="gender" value="male" 
                                       {{ old('gender', $user->gender) == 'male' ? 'checked' : '' }}>
                                <span>Male</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="gender" value="female" 
                                       {{ old('gender', $user->gender) == 'female' ? 'checked' : '' }}>
                                <span>Female</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="gender" value="others" 
                                       {{ old('gender', $user->gender) == 'others' ? 'checked' : '' }}>
                                <span>Others</span>
                            </label>
                        </div>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-8 text-right">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

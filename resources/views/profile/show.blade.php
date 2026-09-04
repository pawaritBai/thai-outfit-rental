<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jomhuria&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            width: 100%;
            /* Full width of screen */
            height: 71px;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Space between logo and user info */
            padding: 0 20px;
            position: fixed;
            top: 0;
            z-index: 100;
        }

        .logo {
            color: #FFFAFA;
            font-family: 'Jomhuria', sans-serif;
            font-size: 64px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="flex items-center">
            <a href="{{ route(str_replace(' ', '', $user->userType) . '.dashboard') }}" class="logo">
                ThaiWijit
            </a>
        </div>
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="m-16 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Profile Header with Name and Edit Button -->
                <div class="profile-header flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Profile Pic -->
                        <img src="{{ asset($user->profilePicture) }}" alt="Profile Picture" class="profile-picture rounded-full h-32 w-32 object-cover mr-6">
                        <div>
                            <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="edit-profile-button text-blue-500 hover:text-blue-700">Edit Profile</a>
                </div>

                <!-- Profile Information -->
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700">Personal Information</h3>
                    <div class="space-y-4 mt-4">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email account:</strong> {{ $user->email }}</p>
                        <p><strong>Mobile number:</strong> {{ $user->phone ?? 'N/A' }}</p>
                        <p><strong>Gender:</strong> {{ $user->gender ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
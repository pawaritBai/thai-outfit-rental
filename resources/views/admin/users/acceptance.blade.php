@extends('layouts.admin-layout')

@section('title', 'User Acceptance')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">รายการรออนุมัติ</h1>
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left mr-2"></i>กลับไปที่รายการผู้ใช้
            </a>
        </div>
        
        <div class="py-6">
            <div class="w-full mx-auto">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="w-full bg-white rounded-lg" id="usersTable">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">ID</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Name</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Email</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Phone</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Username</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Role</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Status</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Document</th>
                                    <th class="p-2 text-center text-xs font-medium text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr id="userRow-{{ $user->user_id }}" class="border-b hover:bg-gray-50">
                                    <td class="p-2 text-center text-sm">{{ $user->user_id }}</td>
                                    <td class="p-2 text-center text-sm">{{ $user->name }}</td>
                                    <td class="p-2 text-center text-sm">
                                        <span class="truncate block max-w-[150px] mx-auto" title="{{ $user->email }}">
                                            {{ $user->email }}
                                        </span>
                                    </td>
                                    <td class="p-2 text-center text-sm">{{ $user->phone }}</td>
                                    <td class="p-2 text-center text-sm">{{ $user->username }}</td>
                                    <td class="p-2 text-center text-sm">{{ $user->userType }}</td>
                                    <td class="p-2 text-center text-sm" id="status-{{ $user->user_id }}">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="p-2 text-center text-sm">
                                        <a href="{{ asset($user->identity_path) }}" target="_blank" 
                                        class="text-blue-500 underline hover:text-blue-700">
                                            View
                                        </a>
                                    </td>
                                    <td class="p-2 text-center text-sm">
                                        <div class="flex justify-center space-x-1">
                                            <button class="text-white bg-blue-500 hover:bg-blue-600 text-xs px-2 py-1 rounded acceptButton" data-user-id="{{ $user->user_id }}">อนุมัติ</button>
                                            <button class="text-white bg-red-500 hover:bg-red-600 text-xs px-2 py-1 rounded declineButton" data-user-id="{{ $user->user_id }}">ปฏิเสธ</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.acceptButton').forEach(button => {
                button.addEventListener('click', function() {
                    updateUserStatus(this.dataset.userId, 'active');
                });
            });

            document.querySelectorAll('.declineButton').forEach(button => {
                button.addEventListener('click', function() {
                    updateUserStatus(this.dataset.userId, 'inactive');
                });
            });

            function updateUserStatus(userId, status) {
                fetch(`/admin/users/${userId}/${status}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`status-${userId}`).textContent = status;
                            if (status === 'active') {
                                document.getElementById(`userRow-${userId}`).remove();
                            }
                        } else {
                            alert('Failed to update status.');
                        }
                    });
            }
        });
    </script>
@endsection
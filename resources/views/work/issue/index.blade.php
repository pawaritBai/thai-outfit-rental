@extends('layouts.staff-dashboard')

@section('title', 'รายการปัญหาที่แจ้ง')

@php
    // Determine route prefix based on user type
    if (Auth::user()->userType == 'photographer') {
        $routePrefix = 'photographer';
    } elseif (Auth::user()->userType == 'make-up artist') {
        $routePrefix = 'make-upartist';
    } else {
        $routePrefix = 'work'; // Fallback
    }
@endphp


@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">รายการปัญหาที่แจ้ง</h2>
        <a href="{{ route($routePrefix.'.issue.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            <i class="fas fa-plus-circle mr-2"></i> แจ้งปัญหาใหม่
        </a>
    </div>

    @if(count($issues) > 0)
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หัวข้อ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่แจ้ง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การตอบกลับ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($issues as $issue)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $issue->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $issue->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($issue->status == 'reported') bg-yellow-100 text-yellow-800
                                    @elseif($issue->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($issue->status == 'fixed') bg-green-100 text-green-800
                                    @endif">
                                    {{ $issue->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($issue->file_path)
                                    <span class="text-green-600"><i class="fas fa-check-circle"></i> มี</span>
                                @else
                                    <span class="text-gray-400">ไม่มี</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($issue->reply)
                                    <span class="text-green-600"><i class="fas fa-comment-alt"></i> มี</span>
                                @else
                                    <span class="text-gray-400">ยังไม่มี</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route($routePrefix.'.issue.show', $issue->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <p class="text-gray-500">ไม่พบรายการแจ้งปัญหา</p>
            <a href="{{ route($routePrefix.'.issue.create') }}" 
               class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus-circle mr-2"></i> แจ้งปัญหาใหม่
            </a>
        </div>
    @endif
</div>
@endsection

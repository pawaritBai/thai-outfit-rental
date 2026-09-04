@extends('layouts.staff-dashboard')

@section('title', 'รายละเอียดปัญหา')

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
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 border-b pb-2">รายละเอียดปัญหา</h2>

        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700">หัวข้อ:</h3>
                <p class="mt-1">{{ $issue->title }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700">รายละเอียด:</h3>
                <p class="mt-1 whitespace-pre-line">{{ $issue->description }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700">สถานะ:</h3>
                <p class="mt-1">
                    <span class="px-3 py-1 rounded-full 
                        @if($issue->status == 'reported') bg-yellow-100 text-yellow-800
                        @elseif($issue->status == 'in_progress') bg-blue-100 text-blue-800
                        @elseif($issue->status == 'fixed') bg-green-100 text-green-800
                        @endif">
                        {{ $issue->status }}
                    </span>
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700">วันที่แจ้ง:</h3>
                <p class="mt-1">{{ $issue->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($issue->file_path)
            <div>
                <h3 class="text-lg font-semibold text-gray-700">รูปภาพประกอบ:</h3>
                <div class="mt-2">
                    <img src="{{ asset($issue->file_path) }}" alt="รูปประกอบปัญหา" 
                         class="max-w-full h-auto rounded-lg shadow-sm border border-gray-200" style="max-height: 400px;">
                </div>
                <p class="text-sm text-blue-600 mt-1">
                    <a href="{{ asset($issue->file_path) }}" target="_blank" class="hover:underline">
                        <i class="fas fa-external-link-alt mr-1"></i> ดูรูปภาพขนาดเต็ม
                    </a>
                </p>
            </div>
            @endif

            @if($issue->reply)
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">การตอบกลับจากผู้ดูแลระบบ:</h3>
                <p class="mt-2 whitespace-pre-line">{{ $issue->reply }}</p>
                <p class="text-sm text-gray-500 mt-2">ตอบกลับเมื่อ: {{ $issue->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @else
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <p class="text-center text-gray-500">ยังไม่มีการตอบกลับจากผู้ดูแลระบบ กรุณารอการตอบกลับ</p>
            </div>
            @endif
        </div>

        <div class="mt-8">
            <a href="{{ route($routePrefix.'.issue.index') }}" 
               class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i> กลับ
            </a>
        </div>
    </div>
</div>
@endsection
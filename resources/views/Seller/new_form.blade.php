@extends('layouts.seller-layout')

@section('title', 'Request Form')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Request Form</h2>

    <div class="bg-white p-6 shadow rounded-lg flex flex-wrap gap-6">
        <!-- Image Upload Section -->
        <div class="w-full md:w-1/3">
            <div class="border border-gray-300 rounded-lg p-10 text-center">
                <i class="fa fa-camera text-4xl text-gray-500"></i>
                <p class="text-gray-500 mt-2">Upload Image</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full md:w-2/3">
            <form class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label class="block text-gray-700">ชื่อชุด:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ระดับชุด:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ประเภทชุด:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ประเภทผ้า:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">สีชุด:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ขนาดชุด:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ราคาต่อวัน:</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="mb-4 flex items-center">
                    <label class="block text-gray-700 mr-2">จำนวนชุดที่ต้องเช่า:</label>
                    <button type="button" class="px-2 py-1 bg-gray-300 rounded">-</button>
                    <input type="text" class="w-12 text-center border border-gray-300 mx-2 rounded-lg p-2" value="1">
                    <button type="button" class="px-2 py-1 bg-gray-300 rounded">+</button>
                </div>
                <div class="flex gap-4">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-black rounded">กลับ</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">เพิ่ม</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

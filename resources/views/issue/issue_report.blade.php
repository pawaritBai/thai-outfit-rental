<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h2 class="text-center text-3xl font-semibold text-gray-900 mb-6 mt-10">ปัญหาที่รายงาน</h2>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mb-6">
        <p><strong class="text-lg font-medium text-gray-700">หัวข้อ:</strong> {{ $issue->title }}</p>
        <p><strong class="text-lg font-medium text-gray-700">อธิบายปัญหา:</strong> {{ $issue->description }}</p>
        <p><strong class="text-lg font-medium text-gray-700">สถานะ:</strong>
            <span class="font-bold 
                    @if($issue->status == 'in_progress') text-yellow-600 
                    @elseif($issue->status == 'fixed') text-green-600 
                    @else text-gray-600 @endif">{{ $issue->status }}</span>
        </p>
        <p><strong class="text-lg font-medium text-gray-700">การอัพเดทล่าสุด:</strong> {{ $issue->updated_at->format('d M Y, H:i') }}</p>
        @if($issue->file_path)
            @php
            // แยกนามสกุลไฟล์จาก path
            $fileExtension = pathinfo($issue->file_path, PATHINFO_EXTENSION);
            @endphp

            @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
            <!-- ถ้าไฟล์เป็นภาพ -->
            <img src="{{ asset($issue->file_path) }}" alt="Issue Pic" class="h-auto w-auto object-contain mr-6">
            @elseif(strtolower($fileExtension) === 'pdf')
            <!-- ถ้าไฟล์เป็น PDF -->
            <a href="{{ asset($issue->file_path) }}" target="_blank" class="text-blue-500 underline">
                คลิกที่นี่เพื่อดูไฟล์ PDF
            </a>
            @else
            <!-- ถ้าไฟล์ไม่ใช่ภาพหรือ PDF -->
            <p>ไฟล์ประเภทนี้ไม่สามารถแสดงได้</p>
            @endif
        @endif
    </div>

    <div class="flex justify-center mt-4">
        <button onclick="window.history.back();"
            class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition duration-200 transform hover:scale-105">
            กลับ
        </button>
    </div>

</body>

</html>
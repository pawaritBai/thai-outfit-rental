@extends('layouts.main')

@section('title', 'Issue')

@section('content')

  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">แจ้งปัญหา</h2>

    <form action="{{ route('report.issue') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <!-- หัวข้อ -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">หัวข้อ</label>
        <input type="text" name="title" id="title" required
               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
      </div>

      <!-- คำอธิบาย -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">อธิบายปัญหา</label>
        <textarea name="description" id="description" required rows="4"
                  class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
      </div>

      <!-- อัปโหลดไฟล์ -->
      <div>
        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">แนบไฟล์ (รูปภาพ / PDF)</label>
        <input type="file" name="file" id="file" accept="image/*,application/pdf"
               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        
        <!-- ตัวอย่างรูปภาพ -->
        <div id="preview" class="mt-4 hidden">
          <p class="text-sm text-gray-600 mb-1">แสดงตัวอย่างรูปภาพ:</p>
          <img id="previewImage" src="#" alt="Preview" class="w-48 h-auto rounded border shadow"/>
        </div>
      </div>

      <!-- ปุ่ม -->
      <div class="flex justify-between mt-6">
        <button type="button" onclick="window.history.back();"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
          กลับ
        </button>
        <button type="submit"
                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">
          ส่งปัญหา
        </button>
      </div>
    </form>
  </div>

  <script>
    // แสดง preview รูปภาพเมื่อเลือกไฟล์
    document.getElementById('file').addEventListener('change', function (event) {
      const file = event.target.files[0];
      const preview = document.getElementById('preview');
      const previewImg = document.getElementById('previewImage');

      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      } else {
        preview.classList.add('hidden');
        previewImg.src = '#';
      }
    });
  </script>

</body>
</html>

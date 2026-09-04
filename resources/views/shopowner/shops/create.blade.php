@extends('layouts.shopowner-layout')

@section('title', 'ลงทะเบียนร้านค้าใหม่')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-6">ลงทะเบียนร้านค้าใหม่</h2>

    <div class="bg-white p-6 shadow rounded-lg">
        <form method="POST" action="{{ route('shopowner.shops.store') }}" class="max-w-4xl mx-auto">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="mb-4">
                    <label for="shop_name" class="block text-gray-700 font-medium mb-2">ชื่อร้านค้า</label>
                    <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- ที่อยู่ร้านค้า -->
                <div class="col-span-2">
                    <h3 class="font-medium text-lg mb-3">ที่อยู่ร้านค้า</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-3">
                            <label for="province" class="block text-gray-700 font-medium mb-2">จังหวัด</label>
                            <select id="province" name="province" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- เลือกจังหวัด --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="district" class="block text-gray-700 font-medium mb-2">อำเภอ</label>
                            <select id="district" name="district" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- เลือกอำเภอ --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subdistrict" class="block text-gray-700 font-medium mb-2">ตำบล</label>
                            <select id="subdistrict" name="subdistrict" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- เลือกตำบล --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="postalCode" class="block text-gray-700 font-medium mb-2">รหัสไปรษณีย์</label>
                            <input type="text" id="postalCode" name="postalCode" readonly
                                class="w-full border bg-gray-100 border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-3">
                            <label for="houseNumber" class="block text-gray-700 font-medium mb-2">บ้านเลขที่</label>
                            <input type="text" id="houseNumber" name="houseNumber" value="{{ old('houseNumber') }}" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-3">
                            <label for="street" class="block text-gray-700 font-medium mb-2">ถนน (ถ้ามี)</label>
                            <input type="text" id="street" name="street" value="{{ old('street') }}"
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="shop_description" class="block text-gray-700 font-medium mb-2">คำอธิบายร้านค้า</label>
                <textarea name="shop_description" id="shop_description" rows="4"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>{{ old('shop_description') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="rental_terms" class="block text-gray-700 font-medium mb-2">เงื่อนไขการเช่า</label>
                <textarea name="rental_terms" id="rental_terms" rows="4"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>{{ old('rental_terms') }}</textarea>
            </div>

            <div class="mt-6 flex items-center gap-4">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    <i class="fa fa-save mr-2"></i> ลงทะเบียนร้านค้า
                </button>
                <a href="{{ route('shopowner.shops.my-shop') }}" class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 font-medium">
                    <i class="fa fa-times mr-2"></i> ยกเลิก
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const subdistrictSelect = document.getElementById('subdistrict');
    const postalCodeInput = document.getElementById('postalCode');
    let addressData = [];

    // Load Thai address data
    window.onload = async () => {
        try {
            const res = await fetch('https://raw.githubusercontent.com/kongvut/thai-province-data/master/api_province_with_amphure_tambon.json');
            
            if (!res.ok) {
                throw new Error('ไม่สามารถโหลดข้อมูลได้');
            }
            
            addressData = await res.json();
            
            // Fill provinces
            addressData.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name_th;
                option.textContent = province.name_th;
                option.dataset.id = province.id;
                provinceSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading address data:', error);
            alert('ไม่สามารถโหลดข้อมูลที่อยู่ได้ กรุณาลองใหม่อีกครั้ง');
        }
    };

    // Handle province change
    provinceSelect.addEventListener('change', () => {
        districtSelect.innerHTML = '<option value="">-- เลือกอำเภอ --</option>';
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';
        
        if (!provinceSelect.value) return;
        
        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceId = selectedOption.dataset.id;
        const province = addressData.find(p => p.id == provinceId);
        
        if (province && province.amphure) {
            province.amphure.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name_th;
                option.textContent = district.name_th;
                option.dataset.id = district.id;
                districtSelect.appendChild(option);
            });
        }
    });

    // Handle district change
    districtSelect.addEventListener('change', () => {
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';
        
        if (!districtSelect.value) return;
        
        const provinceOption = provinceSelect.options[provinceSelect.selectedIndex];
        const districtOption = districtSelect.options[districtSelect.selectedIndex];
        
        const provinceId = provinceOption.dataset.id;
        const districtId = districtOption.dataset.id;
        
        const province = addressData.find(p => p.id == provinceId);
        const district = province?.amphure.find(d => d.id == districtId);
        
        if (district && district.tambon) {
            district.tambon.forEach(subdistrict => {
                const option = document.createElement('option');
                option.value = subdistrict.name_th;
                option.textContent = subdistrict.name_th;
                option.dataset.zip = subdistrict.zip_code;
                subdistrictSelect.appendChild(option);
            });
        }
    });

    // Handle subdistrict change
    subdistrictSelect.addEventListener('change', () => {
        if (!subdistrictSelect.value) return;
        
        const selectedOption = subdistrictSelect.options[subdistrictSelect.selectedIndex];
        postalCodeInput.value = selectedOption.dataset.zip || '';
    });
</script>
@if ($errors->any())
<div class="bg-red-100 text-red-700 p-4 rounded-lg mt-4">
    <h4 class="font-bold">กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</h4>
    <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
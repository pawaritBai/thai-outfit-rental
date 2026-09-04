@extends('layouts.shopowner-layout')

@section('title', 'แก้ไขชุด')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">แก้ไขชุด: {{ $outfit->name }}</h2>
        <a href="{{ route('shopowner.outfits.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fa fa-arrow-left mr-2"></i> กลับ
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('shopowner.outfits.update', $outfit->outfit_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อชุด *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $outfit->name) }}" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">ราคา (บาท) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $outfit->price) }}" min="0" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="depositfee" class="block text-sm font-medium text-gray-700 mb-1">ค่ามัดจำ (บาท) *</label>
                    <input type="number" name="depositfee" id="depositfee" value="{{ old('depositfee', $outfit->depositfee) }}" min="0" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('depositfee')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="penaltyfee" class="block text-sm font-medium text-gray-700 mb-1">ค่าปรับ (บาท) *</label>
                    <input type="number" name="penaltyfee" id="penaltyfee" value="{{ old('penaltyfee', $outfit->penaltyfee) }}" min="0" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('penaltyfee')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">สถานะ *</label>
                    <select name="status" id="status" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="active" {{ old('status', $outfit->status) == 'active' ? 'selected' : '' }}>พร้อมใช้งาน</option>
                        <option value="inactive" {{ old('status', $outfit->status) == 'inactive' ? 'selected' : '' }}>ไม่พร้อมใช้งาน</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด *</label>
                    <textarea name="description" id="description" rows="4" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $outfit->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">รูปภาพ</label>
                    @if($outfit->image)
                        <div class="mb-3">
                            <img src="{{ asset($outfit->image) }}" alt="{{ $outfit->name }}" class="h-32 w-auto object-cover rounded">
                            <p class="text-xs mt-1 text-gray-500">รูปภาพปัจจุบัน (อัปโหลดรูปใหม่เพื่อเปลี่ยนแปลง)</p>
                        </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">รองรับไฟล์ .jpeg, .png, .jpg, .gif ขนาดสูงสุด 2MB</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่ *</label>
                    <div class="mt-2 border rounded-md p-3 max-h-60 overflow-y-auto">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($categories as $category)
                                <div class="flex items-center p-2 hover:bg-gray-50 rounded">
                                    <input type="radio" name="categories[]" id="category_{{ $category->category_id }}" 
                                        value="{{ $category->category_id }}" 
                                        {{ in_array($category->category_id, old('categories', $outfitCategories)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="category_{{ $category->category_id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">{{ $category->category_name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">เลื่อนดูหมวดหมู่ทั้งหมด</p>
                    @error('categories')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-2">ขนาดและสี</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">เลือกขนาด *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            @php
                                // Get current sizes
                                $currentSizes = $outfit->sizeAndColors->pluck('size_id')->unique()->toArray();
                            @endphp
                            
                            @foreach($sizes as $size)
                                <div class="flex items-center">
                                    <input type="checkbox" name="sizes[]" id="size_{{ $size->size_id }}" 
                                        value="{{ $size->size_id }}" 
                                        {{ in_array($size->size_id, old('sizes', $currentSizes)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="size_{{ $size->size_id }}" class="ml-2 text-sm text-gray-700">{{ $size->size }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('sizes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">เลือกสี *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            @php
                                // Get current colors
                                $currentColors = $outfit->sizeAndColors->pluck('color_id')->unique()->toArray();
                            @endphp
                            
                            @foreach($colors as $color)
                                <div class="flex items-center">
                                    <input type="checkbox" name="colors[]" id="color_{{ $color->color_id }}" 
                                        value="{{ $color->color_id }}" 
                                        {{ in_array($color->color_id, old('colors', $currentColors)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <label for="color_{{ $color->color_id }}" class="ml-2 text-sm text-gray-700">{{ $color->color }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('colors')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="stock-matrix" class="mt-4">
                        <h4 class="text-md font-medium mb-2">กำหนดจำนวนสินค้าตามขนาดและสี *</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ขนาด / สี</th>
                                        <!-- Color headers will be populated by JavaScript -->
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Matrix will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">กรอกจำนวนสินค้าสำหรับแต่ละขนาดและสี</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fa fa-save mr-2"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizeInputs = document.querySelectorAll('input[name="sizes[]"]');
        const colorInputs = document.querySelectorAll('input[name="colors[]"]');
        const stockMatrix = document.getElementById('stock-matrix');
        
        // Store current size and color combinations and amounts
        const existingCombinations = {
            @foreach($outfit->sizeAndColors as $item)
                '{{ $item->size_id }}_{{ $item->color_id }}': {{ $item->amount }},
            @endforeach
        };
        
        function updateStockMatrix() {
            const selectedSizes = [];
            const selectedColors = [];
            
            sizeInputs.forEach(input => {
                if (input.checked) {
                    const sizeId = input.value;
                    const sizeText = input.nextElementSibling.textContent.trim();
                    selectedSizes.push({ id: sizeId, text: sizeText });
                }
            });
            
            colorInputs.forEach(input => {
                if (input.checked) {
                    const colorId = input.value;
                    const colorText = input.nextElementSibling.textContent.trim();
                    selectedColors.push({ id: colorId, text: colorText });
                }
            });
            
            // Create matrix table
            const table = stockMatrix.querySelector('table');
            const thead = table.querySelector('thead tr');
            const tbody = table.querySelector('tbody');
            
            // Clear previous content except first header cell
            while (thead.children.length > 1) {
                thead.removeChild(thead.lastChild);
            }
            
            tbody.innerHTML = '';
            
            // If no sizes or colors selected, hide the matrix
            if (selectedSizes.length === 0 || selectedColors.length === 0) {
                table.classList.add('hidden');
                return;
            }
            
            table.classList.remove('hidden');
            
            // Add color headers
            selectedColors.forEach(color => {
                const th = document.createElement('th');
                th.className = 'px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider';
                th.textContent = color.text;
                thead.appendChild(th);
            });
            
            // Create matrix rows
            selectedSizes.forEach(size => {
                const tr = document.createElement('tr');
                
                // Add size label cell
                const tdSize = document.createElement('td');
                tdSize.className = 'px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900';
                tdSize.textContent = size.text;
                tr.appendChild(tdSize);
                
                // Add input cells for each color
                selectedColors.forEach(color => {
                    const tdInput = document.createElement('td');
                    tdInput.className = 'px-4 py-2 whitespace-nowrap text-sm';
                    
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.name = `amount[${size.id}_${color.id}]`;
                    input.min = '0';
                    
                    // Set existing amount if available
                    const key = `${size.id}_${color.id}`;
                    input.value = existingCombinations[key] || '0';
                    
                    input.className = 'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50';
                    
                    tdInput.appendChild(input);
                    tr.appendChild(tdInput);
                });
                
                tbody.appendChild(tr);
            });
        }
        
        // Initialize matrix
        updateStockMatrix();
        
        // Update matrix when sizes or colors change
        sizeInputs.forEach(input => {
            input.addEventListener('change', updateStockMatrix);
        });
        
        colorInputs.forEach(input => {
            input.addEventListener('change', updateStockMatrix);
        });
    });
</script>
@endsection
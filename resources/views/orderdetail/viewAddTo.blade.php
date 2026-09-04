@extends('layouts.main')

@section('content')
<style>
    .hidden {
        display: none;
    }
</style>

<div class="container mx-auto p-6 bg-gray-50">
    <h2 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-3">ทำการสั่งซื้อ</h2>

    <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto">
        <!-- รายการสินค้าในตะกร้า -->
        <div class="lg:w-3/5 space-y-4">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">รายการสินค้า</h3>

            @foreach ($cartItems as $cartItem)
            <div class="bg-white shadow-md rounded-lg p-4 flex gap-6 hover:shadow-lg transition duration-300 border-l-4 {{ $cartItem->overent == 1 ? 'border-yellow-400' : 'border-green-500' }}">
                <img src="{{ asset($cartItem->outfit->image) }}" class="w-28 h-28 object-cover rounded-lg border">

                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            {{ $cartItem->outfit->name }}
                            @if($cartItem->overent == 1)
                            <span class="text-xs bg-yellow-400 text-white px-2 py-1 rounded-full">สั่งเพิ่ม</span>
                            @endif
                        </h4>
                        <div class="flex flex-wrap gap-x-4 mt-1 text-gray-600 text-sm">
                            <p>สี: <span class="font-medium">{{ $cartItem->color->color ?? 'ไม่ระบุ' }}</span></p>
                            <p>ขนาด: <span class="font-medium">{{ $cartItem->size->size ?? 'ไม่ระบุ' }}</span></p>
                            <p>จำนวน: <span class="font-medium">{{ $cartItem->quantity }}</span></p>
                            @if(isset($cartItem->sizeAndColor))
                            <p>คงเหลือ: <span class="font-medium">{{ $cartItem->overent == 1 ? '-' : $cartItem->sizeAndColor->amount }}</span></p>
                            @endif
                        </div>
                        <span class="text-gray-600 text-sm">ร้าน:</span>
                        {{ $cartItem->outfit->shop->shop_name ?? 'ไม่ระบุ' }} <br>
                        <span class="text-gray-600 text-sm">วันที่จองชุด:</span>
                        {{ $cartItem->reservation_date ? date('d/m/Y', strtotime($cartItem->reservation_date)) : 'ไม่ระบุ' }}
                    </div>

                    <div class="flex justify-between items-end mt-2">
                        <p class="text-green-600 font-medium">{{ number_format($cartItem->outfit->price, 2) }} ฿</p>
                        <p class="font-bold text-lg text-gray-800">{{ number_format($cartItem->outfit->price * $cartItem->quantity, 2) }} ฿</p>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500 mt-6">
                <p class="text-sm text-gray-600">* การให้บริการของเราอยู่ภายใต้เงื่อนไขและข้อตกลงการใช้บริการ</p>
            </div>
        </div>

        <!-- แบบฟอร์มการสั่งซื้อ -->
        <div class="lg:w-2/5">
            <div class="bg-white shadow-md rounded-lg p-6 sticky top-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b">รายละเอียดการสั่งซื้อ</h3>

                @php
                // จัดกลุ่ม CartItem ตาม shop_id และ reservation_date
                $groupedItems = $cartItems->groupBy(function ($item) {
                return $item->outfit->shop_id . '|' . $item->reservation_date;
                });
                @endphp

                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    @foreach ($cartItems as $item)
                    <input type="hidden" name="cart_item_ids[]" value="{{ $item->cart_item_id }}">
                    @endforeach

                    <!-- บริการเสริม -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-5">
                        <h4 class="font-medium text-gray-800 mb-3">บริการเสริม</h4>
                        @foreach ($groupedItems as $groupKey => $groupItems)
                        @php
                        [$shopId, $reservationDate] = explode('|', $groupKey);
                        $shopName = $groupItems->first()->outfit->shop->shop_name ?? 'ไม่ระบุ';
                        $formattedDate = $reservationDate ? date('d/m/Y', strtotime($reservationDate)) : 'ไม่ระบุ';
                        $groupId = "service-{$shopId}-{$reservationDate}";
                        @endphp

                        <!-- Checkbox สำหรับเลือกวันที่และร้าน -->
                        <div class="mb-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="enable-{{ $groupId }}" onchange="toggleServiceInput('{{ $groupId }}')" class="w-5 h-5">
                                <span class="text-gray-700">บริการเสริมสำหรับร้าน: {{ $shopName }} (วันที่: {{ $formattedDate }})</span>
                            </label>
                        </div>

                        <!-- ช่องบริการเสริม (ซ่อนเริ่มต้น) -->
                        <div id="{{ $groupId }}" class="hidden pl-6 mb-4">
                            <!-- Photographer -->
                            <div class="mb-4">
                                <label class="flex justify-between text-gray-700 mb-2">
                                    <span>จำนวนลูกค้าที่ต้องการช่างภาพ:</span>
                                    <span class="text-sm text-gray-500">2,000 ฿/คน</span>
                                </label>
                                <div class="flex border border-gray-300 rounded-lg overflow-hidden mb-2">
                                    <button type="button" onclick="decrementCount('photographer_count_{{ $groupId }}')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold">-</button>
                                    <input type="number" id="photographer_count_{{ $groupId }}" name="selected_services[{{ $shopId }}][{{ $reservationDate }}][photographer][count]" min="0" value="0" class="w-full text-center p-2 focus:outline-none" onchange="toggleTimeInput('photographer_time_{{ $groupId }}', this.value)">
                                    <button type="button" onclick="incrementCount('photographer_count_{{ $groupId }}')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold">+</button>
                                </div>
                                <!-- ช่องเลือกเวลา -->
                                <div id="photographer_time_{{ $groupId }}_wrapper" class="hidden">
                                    <label class="block text-gray-700 text-sm mb-1">เลือกเวลา:</label>
                                    <input type="time" id="photographer_time_{{ $groupId }}" name="selected_services[{{ $shopId }}][{{ $reservationDate }}][photographer][time]" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <!-- Makeup -->
                            <div class="mb-2">
                                <label class="flex justify-between text-gray-700 mb-2">
                                    <span>จำนวนลูกค้าที่ต้องการช่างแต่งหน้า:</span>
                                    <span class="text-sm text-gray-500">2,000 ฿/คน</span>
                                </label>
                                <div class="flex border border-gray-300 rounded-lg overflow-hidden mb-2">
                                    <button type="button" onclick="decrementCount('makeup_count_{{ $groupId }}')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold">-</button>
                                    <input type="number" id="makeup_count_{{ $groupId }}" name="selected_services[{{ $shopId }}][{{ $reservationDate }}][makeup][count]" min="0" value="0" class="w-full text-center p-2 focus:outline-none" onchange="toggleTimeInput('makeup_time_{{ $groupId }}', this.value)">
                                    <button type="button" onclick="incrementCount('makeup_count_{{ $groupId }}')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold">+</button>
                                </div>
                                <!-- ช่องเลือกเวลา -->
                                <div id="makeup_time_{{ $groupId }}_wrapper" class="hidden">
                                    <label class="block text-gray-700 text-sm mb-1">เลือกเวลา:</label>
                                    <input type="time" id="makeup_time_{{ $groupId }}" name="selected_services[{{ $shopId }}][{{ $reservationDate }}][makeup][time]" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- ใช้โค้ดโปรโมชัน -->
                    <div class="mb-5">
                        <label class="block text-gray-700 font-medium mb-2">โค้ดส่วนลด (ถ้ามี):</label>
                        <div class="flex gap-2">
                            <input type="text" name="promotion_code" id="promotion_code" placeholder="ใส่โค้ดโปรโมชั่น" class="flex-1 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition" onclick="applyPromotion()">ใช้โค้ด</button>
                        </div>
                    </div>

                    <!-- โค้ดส่วนลดร้านค้า -->
                    @foreach ($outfits->groupBy('shop_id') as $shopId => $shopOutfits)
                    <div class="mb-6 border p-4 rounded-lg">
                        <h2 class="text-lg font-bold mb-2">ร้าน: {{ $shopOutfits->first()->shop->shop_name }}</h2>

                        @if (isset($activePromotions[$shopId]))
                        @php $promo = $activePromotions[$shopId]; @endphp
                        <div class="bg-green-100 p-3 rounded text-green-800 mb-2">
                            โปรโมชั่น: <strong>{{ $promo->promotion_name }}</strong><br>
                            ส่วนลด: {{ $promo->discount_amount }} บาท<br>
                            ใช้ได้ถึง: {{ $promo->end_date }}<br>
                            โค้ดส่วนลด: {{ $promo->promotion_code }}
                        </div>
                        <input type="hidden" name="promotions[{{ $shopId }}]" value="{{ $promo->promotion_id }}">
                        @else
                        <div class="text-gray-500">ไม่มีโปรโมชั่นสำหรับร้านนี้</div>
                        @endif
                    </div>
                    @endforeach


                    <!-- สรุปราคารวม -->
                    <div class="border-t pt-4 mt-4 space-y-2 text-sm text-gray-700">
                        <div class="flex justify-between">
                            <span>ราคาสินค้ารวม:</span>
                            <span id="product_total">{{ number_format($cartItems->sum(fn($i) => $i->overent == 1 ? 0 : $i->quantity * $i->outfit->price), 2) }} ฿</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ค่าบริการช่างภาพและช่างแต่งหน้า:</span>
                            <span id="staff_total">0.00 ฿</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ส่วนลด:</span>
                            <span id="discount_total" class="text-red-500">0.00 ฿</span>
                        </div>
                        <div class="flex justify-between border-t border-dashed pt-3 mt-3 text-base">
                            <span class="font-semibold">ยอดชำระทั้งสิ้น:</span>
                            <span id="grand_total" class="font-bold text-green-600">{{ number_format($cartItems->sum(fn($i) => $i->overent == 1 ? 0 : $i->quantity * $i->outfit->price), 2) }} ฿</span>
                        </div>

                        <!-- เลือกที่อยู่สำหรับการจัดส่ง -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">ที่อยู่สำหรับบริการเสริม:</label>
                            <div class="flex flex-col gap-4">
                                @if($hasAddress)
                                <!-- ใช้ที่อยู่เดียวกับลูกค้า -->
                                <div>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="address_type" value="customer" checked onchange="toggleAddressInput()">
                                        <span>ใช้ที่อยู่เดิม</span>
                                    </label>
                                    <div class="mt-2 ml-6" id="customer-address-select">
                                        <select name="cus_address_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @foreach($customerAddress as $address)
                                            <option value="{{ $address->cus_address_id }}">
                                                @if($address->address)
                                                {{ $address->address->HouseNumber ?? 'ไม่มีบ้านเลขที่' }}
                                                {{ $address->address->Street ?? 'ไม่มีถนน' }}
                                                {{ $address->address->Subdistrict ?? 'ไม่มีตำบล' }}
                                                {{ $address->address->District ?? 'ไม่มีอำเภอ' }}
                                                {{ $address->address->Province ?? 'ไม่มีจังหวัด' }}
                                                {{ $address->address->PostalCode ?? 'ไม่มีรหัสไปรษณีย์' }}
                                                @else
                                                ที่อยู่ไม่สมบูรณ์ (Address ID: {{ $address->address_id ?? 'ไม่ระบุ' }})
                                                @endif
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- กรอกที่อยู่ใหม่ -->
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="address_type" value="custom" onchange="toggleAddressInput()">
                                    <span>กรอกที่อยู่ใหม่</span>
                                </label>
                                @else
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="address_type" value="custom" checked onchange="toggleAddressInput()">
                                    <span>กรอกที่อยู่ใหม่</span>
                                </label>
                                <p class="text-sm text-red-500">* กรุณากรอกที่อยู่ใหม่ เนื่องจากยังไม่มีที่อยู่ลูกค้า</p>
                                @endif
                            </div>
                        </div>

                        <!-- ฟอร์มกรอกที่อยู่ใหม่ -->
                        <div id="custom-address-form" class="bg-gray-50 p-4 rounded-lg mb-5 hidden">
                            <h4 class="font-medium text-gray-800 mb-3">ที่อยู่สำหรับบริการ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">จังหวัด</label>
                                    <select id="staff_province" name="staff_address[province]" class="w-full border border-gray-300 rounded-lg p-3">
                                        <option value="">-- เลือกจังหวัด --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">อำเภอ</label>
                                    <select id="staff_district" name="staff_address[district]" class="w-full border border-gray-300 rounded-lg p-3">
                                        <option value="">-- เลือกอำเภอ --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">ตำบล</label>
                                    <select id="staff_subdistrict" name="staff_address[subdistrict]" class="w-full border border-gray-300 rounded-lg p-3">
                                        <option value="">-- เลือกตำบล --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">รหัสไปรษณีย์</label>
                                    <input type="text" id="staff_postal_code" name="staff_address[postal_code]" readonly class="w-full border border-gray-300 bg-gray-100 rounded-lg p-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 font-medium mb-1">บ้านเลขที่</label>
                                    <input type="text" name="staff_address[detail]" class="w-full border border-gray-300 rounded-lg p-3" placeholder="เช่น 123/4">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 font-medium mb-1">ถนน</label>
                                    <input type="text" name="staff_address[street]" class="w-full border border-gray-300 rounded-lg p-3" placeholder="เช่น สุขุมวิท 24">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มสั่งซื้อ -->
                    <div class="mt-6">
                        <button type="submit" onclick="return validateForm()" class="w-full px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            ยืนยันการสั่งซื้อ
                        </button>
                    </div>

                    <!-- Hidden inputs ที่จำเป็น -->
                    <input type="hidden" id="total_price" name="total_price" value="0">
                    <input type="hidden" id="amount_staff" name="amount_staff" value="0">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDiscount = 0;

    function incrementCount(id) {
        const input = document.getElementById(id);
        let value = parseInt(input.value) || 0;
        input.value = value + 1;
        toggleTimeInput(id.replace('count', 'time'), input.value);
        updateTotal();
    }

    function decrementCount(id) {
        const input = document.getElementById(id);
        let value = parseInt(input.value) || 0;
        if (value > 0) {
            input.value = value - 1;
            toggleTimeInput(id.replace('count', 'time'), input.value);
            updateTotal();
        }
    }

    function toggleServiceInput(groupId) {
        const serviceSection = document.getElementById(groupId);
        const checkbox = document.getElementById(`enable-${groupId}`);

        if (serviceSection) {
            serviceSection.classList.toggle('hidden', !checkbox.checked);
            console.log(`Toggled ${groupId}, hidden: ${serviceSection.classList.contains('hidden')}`);
        } else {
            console.error(`Service section with ID ${groupId} not found`);
        }

        if (!checkbox.checked) {
            const photographerCount = document.getElementById(`photographer_count_${groupId}`);
            const makeupCount = document.getElementById(`makeup_count_${groupId}`);
            const photographerTime = document.getElementById(`photographer_time_${groupId}`);
            const makeupTime = document.getElementById(`makeup_time_${groupId}`);

            if (photographerCount) photographerCount.value = 0;
            if (makeupCount) makeupCount.value = 0;
            if (photographerTime) photographerTime.value = '';
            if (makeupTime) makeupTime.value = '';

            toggleTimeInput(`photographer_time_${groupId}`, 0);
            toggleTimeInput(`makeup_time_${groupId}`, 0);
        }

        updateTotal();
    }

    function toggleTimeInput(timeId, count) {
        const timeWrapper = document.getElementById(`${timeId}_wrapper`);
        const countValue = parseInt(count) || 0;

        if (timeWrapper) {
            timeWrapper.classList.toggle('hidden', countValue <= 0);
            console.log(`Toggled ${timeId}_wrapper, count: ${countValue}, hidden: ${timeWrapper.classList.contains('hidden')}`);
        } else {
            console.error(`Time wrapper with ID ${timeId}_wrapper not found`);
        }
    }

    function updateTotal() {
        const productTotal = {{ $cartItems->sum(fn($i) => $i->overent == 1 ? 0 : $i->quantity * $i->outfit->price) }};
        const staffPrice = 2000;
        let totalPhotographerCount = 0;
        let totalMakeupCount = 0;

        @foreach($groupedItems as $groupKey => $groupItems)
        @php
            [$shopId, $reservationDate] = explode('|', $groupKey);
            $groupId = "service-{$shopId}-{$reservationDate}";
        @endphp
        if (document.getElementById('enable-{{ $groupId }}')?.checked) {
            const photographerCount = parseInt(document.getElementById('photographer_count_{{ $groupId }}')?.value) || 0;
            const makeupCount = parseInt(document.getElementById('makeup_count_{{ $groupId }}')?.value) || 0;
            totalPhotographerCount += photographerCount;
            totalMakeupCount += makeupCount;
        }
        @endforeach

        const staffTotal = (totalPhotographerCount + totalMakeupCount) * staffPrice;
        const grandTotal = productTotal + staffTotal - currentDiscount;

        document.getElementById('total_price').value = grandTotal;
        document.getElementById('amount_staff').value = totalPhotographerCount + totalMakeupCount;

        document.getElementById('staff_total').innerText = staffTotal.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ฿';
        document.getElementById('discount_total').innerText = currentDiscount.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ฿';
        document.getElementById('grand_total').innerText = grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ฿';
    }

    function validateForm() {
        let isValid = true;
        let errorMessage = '';
        const addressType = document.querySelector('input[name="address_type"]:checked').value;

        // Validate additional services
        @foreach($groupedItems as $groupKey => $groupItems)
        @php
            [$shopId, $reservationDate] = explode('|', $groupKey);
        $groupId = "service-{$shopId}-{$reservationDate}";
        @endphp
        if (document.getElementById('enable-{{ $groupId }}')?.checked) {
            const photographerCount = parseInt(document.getElementById('photographer_count_{{ $groupId }}')?.value) || 0;
            const photographerTime = document.getElementById('photographer_time_{{ $groupId }}')?.value;
            const makeupCount = parseInt(document.getElementById('makeup_count_{{ $groupId }}')?.value) || 0;
            const makeupTime = document.getElementById('makeup_time_{{ $groupId }}')?.value;

            if (photographerCount > 0 && !photographerTime) {
                isValid = false;
                errorMessage += `กรุณาเลือกเวลาสำหรับช่างภาพของร้าน {{ $groupItems->first()->outfit->shop->shop_name }} (วันที่: {{ $reservationDate ? date('d/m/Y', strtotime($reservationDate)) : 'ไม่ระบุ' }})\n`;
            }
            if (makeupCount > 0 && !makeupTime) {
                isValid = false;
                errorMessage += `กรุณาเลือกเวลาสำหรับช่างแต่งหน้าของร้าน {{ $groupItems->first()->outfit->shop->shop_name }} (วันที่: {{ $reservationDate ? date('d/m/Y', strtotime($reservationDate)) : 'ไม่ระบุ' }})\n`;
            }
        }
        @endforeach

        // Validate address
        if (addressType === 'custom') {
            const province = document.getElementById('staff_province').value;
            const district = document.getElementById('staff_district').value;
            const subdistrict = document.getElementById('staff_subdistrict').value;
            const postalCode = document.getElementById('staff_postal_code').value;
            const detail = document.querySelector('input[name="staff_address[detail]"]').value;

            if (!province || !district || !subdistrict || !postalCode || !detail) {
                isValid = false;
                errorMessage += 'กรุณากรอกข้อมูลที่อยู่ใหม่ให้ครบถ้วน\n';
            }
        }
        // No additional validation needed for 'customer' type since cus_address_id is already required in the dropdown

        if (!isValid) {
            alert(errorMessage);
            return false;
        }
        return true;
    }

    function toggleAddressInput() {
        const customAddressForm = document.getElementById('custom-address-form');
        const customerAddressSelect = document.getElementById('customer-address-select');
        const selected = document.querySelector('input[name="address_type"]:checked').value;

        if (selected === 'customer') {
            customAddressForm.classList.add('hidden');
            if (customerAddressSelect) customerAddressSelect.classList.remove('hidden');
            // Disable custom address fields to prevent submission
            document.querySelectorAll('#custom-address-form input, #custom-address-form select').forEach(el => {
                el.disabled = true;
            });
        } else {
            customAddressForm.classList.remove('hidden');
            if (customerAddressSelect) customerAddressSelect.classList.add('hidden');
            // Enable custom address fields
            document.querySelectorAll('#custom-address-form input, #custom-address-form select').forEach(el => {
                el.disabled = false;
            });
        }
    }

    const provinceSelect = document.getElementById('staff_province');
    const districtSelect = document.getElementById('staff_district');
    const subdistrictSelect = document.getElementById('staff_subdistrict');
    const postalCodeInput = document.getElementById('staff_postal_code');

    let addressData = [];

    async function loadThaiAddress() {
        try {
            const res = await fetch('https://raw.githubusercontent.com/kongvut/thai-province-data/master/api_province_with_amphure_tambon.json');
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
            alert('ไม่สามารถโหลดข้อมูลที่อยู่ได้');
        }
    }

    provinceSelect.addEventListener('change', () => {
        districtSelect.innerHTML = '<option value="">-- เลือกอำเภอ --</option>';
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';

        const selectedProvince = addressData.find(p => p.name_th === provinceSelect.value);
        selectedProvince?.amphure?.forEach(district => {
            const option = document.createElement('option');
            option.value = district.name_th;
            option.textContent = district.name_th;
            option.dataset.id = district.id;
            districtSelect.appendChild(option);
        });
    });

    districtSelect.addEventListener('change', () => {
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';

        const selectedProvince = addressData.find(p => p.name_th === provinceSelect.value);
        const selectedDistrict = selectedProvince?.amphure?.find(d => d.name_th === districtSelect.value);
        selectedDistrict?.tambon?.forEach(subdistrict => {
            const option = document.createElement('option');
            option.value = subdistrict.name_th;
            option.textContent = subdistrict.name_th;
            option.dataset.zip = subdistrict.zip_code;
            subdistrictSelect.appendChild(option);
        });
    });

    subdistrictSelect.addEventListener('change', () => {
        const zip = subdistrictSelect.options[subdistrictSelect.selectedIndex]?.dataset?.zip;
        postalCodeInput.value = zip || '';
    });

    document.addEventListener("DOMContentLoaded", () => {
        updateTotal();
        toggleAddressInput();
        loadThaiAddress();

        @foreach($groupedItems as $groupKey => $groupItems)
        @php
            [$shopId, $reservationDate] = explode('|', $groupKey);
        $groupId = "service-{$shopId}-{$reservationDate}";
        @endphp
        const checkbox = document.getElementById('enable-{{ $groupId }}');
        if (checkbox) {
            toggleServiceInput('{{ $groupId }}');
            const photographerCount = document.getElementById('photographer_count_{{ $groupId }}')?.value || 0;
            const makeupCount = document.getElementById('makeup_count_{{ $groupId }}')?.value || 0;
            toggleTimeInput('photographer_time_{{ $groupId }}', photographerCount);
            toggleTimeInput('makeup_time_{{ $groupId }}', makeupCount);
        }
        @endforeach
    });

    async function applyPromotion() {
        const code = document.getElementById('promotion_code').value.trim();
        if (!code) return;

        const shopId = {{ $cartItems->first()->outfit->shop_id ?? 0 }};

        try {
            const res = await fetch('/api/check-promotion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code: code,
                    shop_id: shopId
                })
            });

            const data = await res.json();
            if (data.valid) {
                currentDiscount = data.discount;
                updateTotal();
            } else {
                alert(data.message);
                currentDiscount = 0;
                updateTotal();
            }
        } catch (err) {
            alert("เกิดข้อผิดพลาดในการใช้โปรโมชั่น");
            console.error(err);
        }
    }
</script>
@endsection
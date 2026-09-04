@extends('layouts.main')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-bold mb-6">Shopping Cart</h2>

    @if($cartItems->isEmpty())
        <p class="text-gray-600">ไม่มีสินค้าในตะกร้า</p>
    @else

    @php
        // เรียงลำดับ overent = 0 ก่อน
        $sortedItems = $cartItems->sortBy('overent');

        // เตรียม list ของ outfit+size+color ที่มี overent = 0 เพื่อใช้เช็ค
        $inStockMap = $cartItems->where('overent', 0)->map(function ($item) {
            return $item->outfit_id . '-' . $item->size_id . '-' . $item->color_id;
        })->values()->all();

        // วันที่ปัจจุบัน
        $currentDate = now()->startOfDay();
    @endphp

    <div class="space-y-4">
    @foreach($sortedItems as $cartItem)
        @php
            $key = $cartItem->outfit_id . '-' . $cartItem->size_id . '-' . $cartItem->color_id;
            $isSelectable = $cartItem->overent == 0 || in_array($key, $inStockMap);
            $reservationDate = $cartItem->reservation_date ? \Carbon\Carbon::parse($cartItem->reservation_date)->startOfDay() : null;
            $isDatePassed = $reservationDate && $reservationDate->lessThan($currentDate);
            $isStockInsufficient = $cartItem->overent == 0 && $cartItem->stockRemaining < $cartItem->quantity;
        @endphp

        <div id="cart-item-{{ $cartItem->cart_item_id }}" class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md {{ $isDatePassed || $isStockInsufficient ? 'border border-red-500' : '' }}">
            
            <!-- ✅ Checkbox -->
            <input type="checkbox"
                name="cart_item_ids[]"
                value="{{ $cartItem->cart_item_id }}"
                class="w-5 h-5 border-gray-300 rounded mr-4"
                {{ (!$isSelectable || $isDatePassed || $isStockInsufficient) ? 'disabled' : '' }}
                title="{{ $isDatePassed ? 'วันที่จองล่วงเลยมาแล้ว' : ($isStockInsufficient ? 'สินค้าคงเหลือไม่เพียงพอ' : ($isSelectable ? '' : 'ต้องเลือกสินค้าที่มีในร้านก่อน')) }}">

            <!-- รูปสินค้า -->
            <div class="flex items-center">
                <img src="{{ $cartItem->outfit->image ? asset($cartItem->outfit->image) : asset('images/default-placeholder.png') }}" 
                    class="w-24 h-24 rounded-lg object-cover">

                <div class="ml-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        {{ $cartItem->outfit->name }}
                        @if($cartItem->overent == 1)
                            <span class="text-xs bg-yellow-400 text-white px-2 py-1 rounded-full">สั่งเพิ่ม</span>
                        @endif
                    </h3>
                    <h6 class="text-sm font-semibold flex items-center gap-2 mt-2">
                        @if($cartItem->overent == 1)
                            
                        ร้าน: {{ $cartItem->thaioutfit_sizeandcolor->outfit->shop->shop_name ?? 'ไม่ระบุร้าน' }}
                        @else
                            ร้าน: {{ $cartItem->thaioutfit_sizeandcolor->outfit->shop->shop_name ?? 'ไม่ระบุร้าน' }}
                        @endif
                    </h6>
                    <p class="text-green-600 font-bold mt-2">{{ number_format($cartItem->outfit->price, 0) }}฿ /1 days</p>
                    
                    <p class="text-gray-600 text-sm mt-2">
                        <span class="font-medium">วันที่จอง:</span> 
                        {{ $cartItem->reservation_date ? date('d/m/Y', strtotime($cartItem->reservation_date)) : 'ไม่ระบุ' }}
                        @if($isDatePassed)
                            <span class="text-red-500 text-xs">(ล่วงเลยมาแล้ว)</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- สี -->
            <div class="ml-4">
                <p class="text-md font-semibold">สี: 
                    <span class="text-gray-700">
                        {{ $cartItem->color->color ?? 'ไม่ระบุสี' }}
                    </span>
                </p>
            </div>

            <!-- ขนาด -->
            <div class="ml-4">
                <p class="text-md font-semibold">ขนาด: 
                    <span class="text-gray-700">
                        {{ $cartItem->size->size ?? 'ไม่ระบุขนาด' }}
                    </span>
                </p>
            </div>

            <!-- คงเหลือ -->
            <div class="stock-status">
                <p class="text-sm text-gray-500">
                    คงเหลือ: 
                    {{ $cartItem->overent == 1 ? '-' : ($cartItem->stockRemaining ?? 'ไม่ระบุ') }}
                    @if($isStockInsufficient)
                        <span class="text-red-500 text-xs">(ไม่เพียงพอ)</span>
                    @endif
                </p>
            </div>

            <!-- จำนวน -->
            <div class="flex items-center">
                <button class="px-2 py-1 border rounded-md bg-gray-200 {{ $isDatePassed ? 'cursor-not-allowed opacity-50' : '' }}" 
                    onclick="updateQty('{{ $cartItem->cart_item_id }}', -1, '{{ $cartItem->overent == 1 ? 'null' : ($cartItem->stockRemaining ?? 0) }}', {{ $isDatePassed ? 'true' : 'false' }})" 
                    {{ $isDatePassed ? 'disabled' : '' }}>-</button>

                <input type="number" id="qty-{{ $cartItem->cart_item_id }}" 
                    value="{{ $cartItem->quantity }}" 
                    min="1"
                    class="w-12 text-center border rounded-md {{ $isDatePassed ? 'bg-gray-200' : '' }}"
                    onblur="updateQtyFromInput('{{ $cartItem->cart_item_id }}', '{{ $cartItem->overent == 1 ? 'null' : ($cartItem->stockRemaining ?? 0) }}', {{ $isDatePassed ? 'true' : 'false' }})"
                    {{ $isDatePassed ? 'readonly' : '' }}>

                <button class="px-2 py-1 border rounded-md bg-gray-200 {{ $isDatePassed ? 'cursor-not-allowed opacity-50' : '' }}" 
                    onclick="updateQty('{{ $cartItem->cart_item_id }}', 1, '{{ $cartItem->overent == 1 ? 'null' : ($cartItem->stockRemaining ?? 0) }}', {{ $isDatePassed ? 'true' : 'false' }})" 
                    {{ $isDatePassed ? 'disabled' : '' }}>+</button>
            </div>

            <!-- ปุ่มลบ -->
            <form action="{{ route('cartItem.deleteItem') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cart_id" value="{{ $cartItem->cart_item_id }}">
                <button type="submit" class="text-red-500 hover:text-red-700 text-xl">
                    ❌
                </button>
            </form>
        </div>
    @endforeach
    </div>

    <!-- ปุ่มสั่งจอง -->
    <form id="checkout-form" action="{{ route('orderdetail.viewAddTo') }}" method="POST">
        @csrf
        <input type="hidden" name="cart_item_ids" id="selected-cart-items">
        <button type="button" onclick="submitSelectedItems()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
            ดำเนินการสั่งซื้อ
        </button>
    </form>

    @endif
</div>

<script>
    function updateQty(cartId, change, maxStock, isDatePassed) {
        if (isDatePassed) {
            alert("ไม่สามารถแก้ไขจำนวนได้ เนื่องจากวันที่จองได้ล่วงเลยมาแล้ว");
            return;
        }

        let qtyInput = document.getElementById('qty-' + cartId);
        let newQty = parseInt(qtyInput.value) + change;

        if (newQty < 1) {
            alert("จำนวนสินค้าต้องไม่น้อยกว่า 1");
            return;
        }

        if (maxStock !== 'null' && newQty > parseInt(maxStock)) {
            alert("ชุดคงเหลือไม่เพียงพอ! คงเหลือ: " + maxStock);
            return;
        }

        updateCartItem(cartId, newQty);
    }

    function updateQtyFromInput(cartId, maxStock, isDatePassed) {
        if (isDatePassed) {
            alert("ไม่สามารถแก้ไขจำนวนได้ เนื่องจากวันที่จองได้ล่วงเลยมาแล้ว");
            return;
        }

        let qtyInput = document.getElementById('qty-' + cartId);
        let newQty = parseInt(qtyInput.value);

        if (isNaN(newQty) || newQty < 1) {
            alert("จำนวนสินค้าต้องไม่น้อยกว่า 1");
            qtyInput.value = qtyInput.defaultValue; // รีเซ็ตกลับไปค่าเดิม
            return;
        }

        if (maxStock !== 'null' && newQty > parseInt(maxStock)) {
            alert("ชุดคงเหลือไม่เพียงพอ! คงเหลือ: " + maxStock);
            qtyInput.value = qtyInput.defaultValue; // รีเซ็ตกลับไปค่าเดิม
            return;
        }

        updateCartItem(cartId, newQty);
    }

    function updateCartItem(cartId, newQty) {
        fetch("{{ route('cartItem.updateItem') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify({
                cart_id: cartId,
                quantity: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let qtyInput = document.getElementById('qty-' + cartId);
                qtyInput.value = newQty;
                qtyInput.defaultValue = newQty; // อัพเดทค่า default ด้วย
                // อัพเดท UI และสถานะ
                updateItemStatus(cartId, newQty);
            } else {
                alert(data.message);
                document.getElementById('qty-' + cartId).value = qtyInput.defaultValue;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById('qty-' + cartId).value = qtyInput.defaultValue;
        });
    }

    function updateItemStatus(cartId, newQty) {
        let itemDiv = document.getElementById('cart-item-' + cartId);
        let checkbox = document.querySelector(`input[value="${cartId}"]`);
        let stockStatus = itemDiv.querySelector('.stock-status p');

        @foreach($cartItems as $cartItem)
            if (cartId === '{{ $cartItem->cart_item_id }}') {
                let stockRemaining = {{ $cartItem->overent == 1 ? 'null' : ($cartItem->stockRemaining ?? 0) }};
                let isDatePassed = {{ $isDatePassed ? 'true' : 'false' }};
                let isSelectable = {{ $isSelectable ? 'true' : 'false' }};
                let isStockInsufficient = stockRemaining !== null && newQty > stockRemaining;

                // อัพเดทขอบสีแดง
                if (isStockInsufficient || isDatePassed) {
                    itemDiv.classList.add('border', 'border-red-500');
                } else {
                    itemDiv.classList.remove('border', 'border-red-500');
                }

                // อัพเดทข้อความสถานะสต็อก
                if (stockRemaining !== null) {
                    stockStatus.innerHTML = `คงเหลือ: ${stockRemaining}` + 
                        (isStockInsufficient ? ' <span class="text-red-500 text-xs">(ไม่เพียงพอ)</span>' : '');
                }

                // อัพเดท checkbox
                if (isSelectable && !isStockInsufficient && !isDatePassed) {
                    checkbox.disabled = false;
                    checkbox.title = '';
                } else {
                    checkbox.disabled = true;
                    checkbox.title = isDatePassed ? 'วันที่จองล่วงเลยมาแล้ว' : 
                                    (isStockInsufficient ? 'สินค้าคงเหลือไม่เพียงพอ' : 'ต้องเลือกสินค้าที่มีในร้านก่อน');
                }
            }
        @endforeach
    }

    function submitSelectedItems() {
        let selectedItems = [];
        document.querySelectorAll('input[name="cart_item_ids[]"]').forEach((checkbox) => {
            if (checkbox.checked) {
                selectedItems.push(checkbox.value);
            }
        });

        if (selectedItems.length === 0) {
            alert("กรุณาเลือกสินค้าอย่างน้อย 1 รายการ");
            return;
        }

        document.getElementById('selected-cart-items').value = JSON.stringify(selectedItems);
        document.getElementById('checkout-form').submit();
    }

    // เพิ่ม event listener เมื่อหน้าโหลด
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($cartItems as $cartItem)
            updateItemStatus('{{ $cartItem->cart_item_id }}', {{ $cartItem->quantity }});
        @endforeach
    });
</script>
@endsection
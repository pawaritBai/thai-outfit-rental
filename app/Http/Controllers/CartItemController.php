<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ThaiOutfit;
use App\Models\CartItem;
use App\Models\OrderDetail;
use App\Models\Booking;
use App\Models\User;
use App\Models\ThaiOutfitSizeAndColor;


class CartItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
{
    $user = Auth::user();

    // ดึงข้อมูลสินค้าทั้งหมดในตะกร้าของ User นั้นๆ
    $cartItems = CartItem::with(['outfit', 'size', 'color','thaioutfit_sizeandcolor.outfit.shop'])
            ->where('userId', $user->user_id)
            ->where('status', 'INUSE')
            ->orderBy('overent', 'asc') // ✅ เพิ่มตรงนี้: ให้ overent = 0 มาก่อน
            ->orderBy('outfit_id')
            ->get();


    // ดึงข้อมูล stock_quantity ของแต่ละชุดที่เลือกในตะกร้า
    foreach ($cartItems as $cartItem) {
        $cartItem->sizeAndColor = ThaiOutfitSizeAndColor::where('outfit_id', $cartItem->outfit_id)
            ->where('size_id', $cartItem->size_id)
            ->where('color_id', $cartItem->color_id)
            ->first();

        // ✅ ตรวจสอบว่าค่าถูกต้องหรือไม่
        if (!$cartItem->sizeAndColor || !$cartItem->sizeAndColor->thaioutfit_sizeandcolor) {
            $cartItem->sizeAndColor = (object) ['amount' => 0]; // กำหนดค่าเริ่มต้นให้เป็น 0
        }else{
            $cartItem->shop_name = $cartItem->thaioutfit_sizeandcolor->outfit->shop->shop_name;
        }
        
        $sizeDetail_id = $cartItem->sizeDetail_id;
        $date = $cartItem->reservation_date;
        // คำนวณสินค้าคงเหลือ โดยการลบปริมาณที่ถูกสั่งไปจาก orderdetails
        $stockData = Booking::query()
        ->join('OrderDetails', 'Bookings.booking_id', '=', 'OrderDetails.booking_id')
        ->join('CartItems', 'OrderDetails.cart_item_id', '=', 'CartItems.cart_item_id')
        ->where('CartItems.sizeDetail_id', $sizeDetail_id)
        ->where('Bookings.status', '!=', 'cancelled')
        ->where('OrderDetails.reservation_date', $date)
        ->groupBy('CartItems.sizeDetail_id')
        ->select([
            'CartItems.sizeDetail_id', // อยู่ใน GROUP BY
            \DB::raw('SUM(OrderDetails.quantity) as total_quantity'), // ผลรวมของ quantity
             // ผลรวมของ total
        ])
        ->get(); // หายอดรวม quantity จาก orderdetails

        
        $soldQuantity = $stockData->first()->total_quantity ?? 0;
        if($cartItem->overent){
            $stock = 9999999999;
        }else{
            $stock = $cartItem->thaioutfit_sizeandcolor->amount;
        }
        // dd($cartItem->thaioutfit_sizeandcolor->amount);
    
        // คำนวณสินค้าคงเหลือ
        $cartItem->stockRemaining = $stock - $soldQuantity;

        // เพิ่มค่า stockRemaining ให้กับ attribute ที่สามารถใช้ใน view ได้
        $cartItem->append('stockRemaining');
        $cartItem->append('shop_name');
    }

    // dd($cartItems);

    

    return view('cartItem.index', compact('cartItems'));
}
    


public function addToCart(Request $request)
{
    
    $action = $request->input('action');

    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนเพิ่มลงตะกร้า');
    }

    $user = Auth::user();
    $outfit_id = $request->input('outfit_id');
    $size_id = $request->input('size_id');
    $color_id = $request->input('color_id');
    $quantity = (int) $request->input('quantity', 1);
    $overent = $request->input('overent');
    $sizeDetail_id = $request->input('sizeDetail_id');
    $reservation_date = $request->input('reservation_date');

    $sizeAndColor = ThaiOutfitSizeAndColor::where('outfit_id', $outfit_id)
        ->where('size_id', $size_id)
        ->where('color_id', $color_id)
        ->first();

    if (!$sizeAndColor && $overent == 0) {
        return redirect()->back()->with('error', 'ไม่พบข้อมูลขนาดและสีของชุด');
    }

    $item = CartItem::where('outfit_id', $outfit_id)
        ->where('size_id', $size_id)
        ->where('color_id', $color_id)
        ->where('userId', $user->user_id)
        ->where('overent', $overent)
        ->where('status', 'INUSE')
        ->where('reservation_date', $reservation_date)
        ->first();

    $existingQty = $item ? $item->quantity : 0;

    // ✅ ถ้า overent == 0 ให้คำนวณ max ที่สามารถเพิ่มได้
    if ($overent == 0) {
        $maxQty = $sizeAndColor->amount - $existingQty;

        if ($maxQty <= 0) {
            return redirect()->back()->with('error', 'จำนวนสินค้าที่มีอยู่ในตะกร้าครบตามสต็อกแล้ว');
        }

        if ($quantity > $maxQty) {
            $quantity = $maxQty; // ปรับให้ไม่เกิน
        }
    }

    // ✅ เพิ่มหรือสร้างใหม่
    if ($item) {
        $item->quantity += $quantity;
        $item->save();
    } else {
        CartItem::create([
            'userId' => $user->user_id,
            'outfit_id' => $outfit_id,
            'size_id' => $size_id,
            'color_id' => $color_id,
            'quantity' => $quantity,
            'overent' => $overent,
            'sizeDetail_id' => $sizeDetail_id,
            'reservation_date' => $reservation_date,
        ]);
    }

    if ($action === 'rent') {
        return redirect()->route('cartItem.allItem')->with('success', 'ทำการเช่าเรียบร้อย');
    }else{
        return redirect()->back()->with('success', 'เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว (จำนวนที่เพิ่ม: ' . $quantity . ')');
    }

}





    public function deleteItem(Request $request)
{
    $cart_id = $request->input('cart_id'); // รับค่า cart_id จากฟอร์ม
    $cartItem = CartItem::find($cart_id);

    if (!$cartItem) {
        return redirect()->back()->with('error', 'ไม่พบสินค้าที่ต้องการลบ');
    }

    // เปลี่ยนสถานะเป็น 'REMOVED' แทนการลบ
    $cartItem->status = 'REMOVED';
    $cartItem->save();

    return redirect()->back()->with('success', 'นำสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
}



    public function updateItem(Request $request)
    {
        $cartItem = CartItem::find($request->cart_id);

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'ไม่พบสินค้าที่ต้องการอัปเดต'], 404);
        }

        // ดึงข้อมูล stock
        $sizeAndColor = ThaiOutfitSizeAndColor::where('outfit_id', $cartItem->outfit_id)
            ->where('size_id', $cartItem->size_id)
            ->where('color_id', $cartItem->color_id)
            // ->where('status', 'INUSE')
            ->first();

        if (!$sizeAndColor || $sizeAndColor->amount === null) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลสต็อกสินค้า'], 404);
        }

        if ($request->quantity > $sizeAndColor->amount) {
            return response()->json([
                'success' => false,
                'message' => 'จำนวนสินค้าเกินจากที่มีในสต็อก! คงเหลือ: ' . $sizeAndColor->amount
            ], 400);
        }

        if ($request->quantity < 1) {
            return response()->json(['success' => false, 'message' => 'จำนวนสินค้าต้องไม่น้อยกว่า 1'], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['success' => true, 'message' => 'อัปเดตจำนวนสินค้าเรียบร้อย']);
    }



    

}

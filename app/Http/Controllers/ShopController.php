<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Address;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    

    public function index(Request $request)
    {
        // รับค่าการจัดเรียง
        $orderBy = $request->input('orderBy') ?: 'shop_id';
        $direction = $request->input('direction') ?: 'asc';


        $query = Shop::with('user','address');
        // dd($orderBy);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('shop_id', 'like', "%{$search}%")
                ->orWhere('shop_name', 'like', "%{$search}%");
        }
        

        // จัดเรียงตาม orderBy และ direction
        $query->orderBy($orderBy, $direction);
        // dd($query);

        $shops = $query->get();
        // dd($shops);

        return view('admin.shops.index', compact('shops'));
    }



    public function toggleStatus(Request $request, $shop_id)
    {
        try {
            $shop = Shop::findOrFail($shop_id);
            $shop->status = $request->status;
            $shop->save();
            
            // Check if request is AJAX
            if ($request->ajax() || $request->has('is_ajax')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shop status updated successfully!'
                ]);
            }
            
            // For non-AJAX requests, redirect to shops index
            return redirect()->route('admin.shops.index')->with('success', 'Shop status updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->has('is_ajax')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating shop status: ' . $e->getMessage()
                ], 500);
            }
            
            // For non-AJAX requests, redirect back with error
            return redirect()->back()->with('error', 'Error updating shop status: ' . $e->getMessage());
        }
    }



    public function edit($shop_id)
    {
        $shop = Shop::with('address')->findOrFail($shop_id);
        return view('admin.shops.edit', compact('shop'));
    }


    public function update(Request $request, $id)
    {
        \Log::info('Shop update request data:', $request->all());
        
        $shop = Shop::with('address')->findOrFail($id);
    
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'required|string',
            'rental_terms' => 'required|string',
            'houseNumber' => 'required|string',
            'province' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'postalCode' => 'required|string',
        ]);
    
        DB::beginTransaction();
    
        try {
            // อัปเดตข้อมูล shop
            $shop->shop_name = $request->shop_name;
            $shop->shop_description = $request->shop_description;
            $shop->rental_terms = $request->rental_terms;
    
            // จัดการ address
            if ($shop->address) {
                \Log::info('Updating existing address:', ['AddressID' => $shop->address->AddressID]);
                $address = $shop->address;
                $address->HouseNumber = $request->houseNumber;
                $address->Street = $request->street ?? '';
                $address->Subdistrict = $request->subdistrict;
                $address->District = $request->district;
                $address->Province = $request->province;
                $address->PostalCode = $request->postalCode;
                $address->save();
            } else {
                \Log::info('Creating new address for shop update');
                $address = new Address();
                $address->HouseNumber = $request->houseNumber;
                $address->Street = $request->street ?? '';
                $address->Subdistrict = $request->subdistrict;
                $address->District = $request->district;
                $address->Province = $request->province;
                $address->PostalCode = $request->postalCode;
                $address->save();
                
                $shop->AddressID = $address->AddressID;
            }
    
            $shop->save();
            
            DB::commit();
            \Log::info('Shop and address updated successfully');
    
            // การ redirect ตามประเภทผู้ใช้
            if (Auth::user()->userType == 'admin') {
                if ($request->ajax() || $request->has('is_ajax')) {
                    return response()->json(['success' => true, 'message' => 'Shop updated successfully']);
                }
                return redirect()->route('admin.shops.index')->with('success', 'Shop updated successfully');
            }
    
            // สำหรับผู้ใช้ทั่วไป (ไม่ใช่ admin)
            if ($request->ajax() || $request->has('is_ajax')) {
                return response()->json(['success' => true, 'message' => 'ข้อมูลร้านค้าอัปเดตเรียบร้อยแล้ว']);
            }
            return redirect()->route('dashboard')->with('success', 'ข้อมูลร้านค้าอัปเดตเรียบร้อยแล้ว');
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating shop:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            if ($request->ajax() || $request->has('is_ajax')) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }



    public function acceptance()
    {
        // ดึงข้อมูลผู้ใช้ทั้งหมด
        $shop = Shop::where(function ($query) {
            $query->where('status', 'inactive')
                ->where('is_newShop', true);
        })
            ->get();


        // ส่งข้อมูลไปยัง view
        return view('admin.shops.acceptance', compact('shop'));
    }


    public function updateStatus(Request $request, $shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)->firstOrFail();
        $shop->status = $request->status;
        $shop->is_newShop = false;
        $shop->save();

        return redirect()->route('admin.shops.acceptance');
    }


    // เพิ่มเมธอดสำหรับสร้างร้านค้าใหม่
public function create()
{
    return view('shopowner.shops.create');
}

// เมธอดสำหรับบันทึกร้านค้าใหม่
public function store(Request $request)
{
    // Dump the request data to see what's coming in (remove in production)
    \Log::info('Shop creation request data:', $request->all());
    
    $request->validate([
        'shop_name' => 'required|string|max:255',
        'shop_description' => 'required|string',
        'rental_terms' => 'required|string',
        'houseNumber' => 'required|string',
        'province' => 'required|string',
        'district' => 'required|string',
        'subdistrict' => 'required|string',
        'postalCode' => 'required|string',
    ]);

    // Begin transaction
    DB::beginTransaction();

    try {
        // Create address first with explicit logging
        \Log::info('Creating address with:', [
            'HouseNumber' => $request->houseNumber,
            'Street' => $request->street,
            'Subdistrict' => $request->subdistrict,
            'District' => $request->district,
            'Province' => $request->province,
            'PostalCode' => $request->postalCode,
        ]);
        
        $address = new Address();
        $address->HouseNumber = $request->houseNumber;
        $address->Street = $request->street ?? '';
        $address->Subdistrict = $request->subdistrict;
        $address->District = $request->district;
        $address->Province = $request->province;
        $address->PostalCode = $request->postalCode;
        $address->save();
        
        \Log::info('Address created with ID:', ['AddressID' => $address->AddressID]);

        // Create shop with address ID
        $shop = new Shop();
        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->rental_terms = $request->rental_terms;
        $shop->status = 'inactive'; // waiting for admin approval
        $shop->is_newShop = true;
        $shop->shop_owner_id = auth()->id();
        $shop->AddressID = $address->AddressID;
        $shop->save();
        
        \Log::info('Shop created with ID:', ['shop_id' => $shop->shop_id]);

        DB::commit();

        return redirect()->route('shopowner.shops.my-shop')
            ->with('success', 'ร้านค้าของคุณถูกส่งไปรอการอนุมัติแล้ว');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating shop:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
    }
}

// เมธอดสำหรับดูร้านค้าของตัวเอง
public function myShop()
{
    $shop = Shop::where('shop_owner_id', auth()->id())->first();
    
    return view('shopowner.shops.my-shop', compact('shop'));
}

// เมธอดสำหรับแก้ไขร้านค้าของตัวเอง
public function editMyShop($shop_id)
{
    $shop = Shop::with('address')->where('shop_id', $shop_id)
        ->where('shop_owner_id', auth()->id())
        ->firstOrFail();
        
    return view('shopowner.shops.edit-my-shop', compact('shop'));
}

// เมธอดสำหรับอัปเดตร้านค้าของตัวเอง
public function updateMyShop(Request $request, $shop_id)
{
    \Log::info('Shop update request data:', $request->all());
    
    $shop = Shop::with('address')->where('shop_id', $shop_id)
        ->where('shop_owner_id', auth()->id())
        ->firstOrFail();
        
    $request->validate([
        'shop_name' => 'required|string|max:255',
        'shop_description' => 'required|string',
        'rental_terms' => 'required|string',
        'houseNumber' => 'required|string',
        'province' => 'required|string',
        'district' => 'required|string',
        'subdistrict' => 'required|string',
        'postalCode' => 'required|string',
    ]);

    DB::beginTransaction();

    try {
        // Update shop details
        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->rental_terms = $request->rental_terms;
        
        // If address exists, update it
        if ($shop->address) {
            \Log::info('Updating existing address:', ['AddressID' => $shop->address->AddressID]);
            $address = $shop->address;
            $address->HouseNumber = $request->houseNumber;
            $address->Street = $request->street ?? '';
            $address->Subdistrict = $request->subdistrict;
            $address->District = $request->district;
            $address->Province = $request->province;
            $address->PostalCode = $request->postalCode;
            $address->save();
        } else {
            // Create new address
            \Log::info('Creating new address for shop update');
            $address = new Address();
            $address->HouseNumber = $request->houseNumber;
            $address->Street = $request->street ?? '';
            $address->Subdistrict = $request->subdistrict;
            $address->District = $request->district;
            $address->Province = $request->province;
            $address->PostalCode = $request->postalCode;
            $address->save();
            
            $shop->AddressID = $address->AddressID;
        }
        
        $shop->save();
        
        DB::commit();
        \Log::info('Shop and address updated successfully');
        
        return redirect()->route('shopowner.shops.my-shop')
            ->with('success', 'ข้อมูลร้านค้าอัปเดตเรียบร้อยแล้ว');
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating shop:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
    }
    }
// แสดงรายการชุดทั้งหมดของร้าน
public function listCostumes()
{
    // ในอนาคตจะต้องดึงข้อมูลจริงจากฐานข้อมูล
    return view('shopowner.shops.costumes.index');
}

// แสดงฟอร์มเพิ่มชุดใหม่
public function newForm()
{
    return view('shopowner.shops.costumes.create');
}

// บันทึกข้อมูลชุดใหม่
public function storeCostume(Request $request)
{
    // ตรวจสอบข้อมูล
    $request->validate([
        'costume_name' => 'required|string|max:255',
        'costume_level' => 'required|string',
        'costume_type' => 'required|string',
        'fabric_type' => 'required|string',
        'costume_color' => 'required|string',
        'costume_size' => 'required|string',
        'price_per_day' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'costume_image' => 'nullable|image|max:2048',
    ]);

    // บันทึกข้อมูล (ต้องสร้างโมเดลสำหรับชุด)
    
    return redirect()->route('shopowner.shop.costumes')
        ->with('success', 'เพิ่มชุดใหม่เรียบร้อยแล้ว');
}
}

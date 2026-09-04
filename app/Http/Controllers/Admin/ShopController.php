<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Display a listing of the shops.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Shop::query();
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_id', 'like', "%{$search}%")
                  ->orWhere('shop_name', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $orderBy = $request->input('orderBy', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($orderBy, $direction);
        
        $shops = $query->get();
        
        return view('admin.shops.index', compact('shops'));
    }
    
    /**
     * Show shops waiting for approval.
     *
     * @return \Illuminate\View\View
     */
    public function acceptance()
    {
        $pendingShops = Shop::where('status', 'pending')->orderBy('created_at', 'asc')->get();
        return view('admin.shops.acceptance', compact('pendingShops'));
    }
    
    /**
     * Toggle the status of a shop.
     *
     * @param int $shop_id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        $newStatus = request('status');
        $shop->status = $newStatus;
        $shop->save();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'shop' => $shop
            ]);
        }
        
        return redirect()->route('admin.shops.index', [
            'orderBy' => request('orderBy'),
            'direction' => request('direction')
        ])->with('success', 'Shop status updated successfully');
    }
    
    /**
     * Update the status of a shop from the acceptance queue.
     *
     * @param int $shop_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        $newStatus = request('status');
        $shop->status = $newStatus;
        $shop->save();
        
        return redirect()->route('admin.shops.acceptance')
            ->with('success', 'Shop status updated to ' . $newStatus);
    }
    
    /**
     * Show the form for editing the specified shop.
     *
     * @param int $shop_id
     * @return \Illuminate\View\View
     */
    public function edit($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        return view('admin.shops.edit', compact('shop'));
    }
    
    /**
     * Update the specified shop in storage.
     *
     * @param Request $request
     * @param int $shop_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $shop_id)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'required|string',
            'shop_location' => 'required|string|max:255',
            'shop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $shop = Shop::findOrFail($shop_id);
        
        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->shop_location = $request->shop_location;
        
        // Handle image upload if provided
        if ($request->hasFile('shop_image')) {
            // Delete old image if exists
            if ($shop->shop_image && Storage::exists('public/' . $shop->shop_image)) {
                Storage::delete('public/' . $shop->shop_image);
            }
            
            $imagePath = $request->file('shop_image')->store('shops', 'public');
            $shop->shop_image = $imagePath;
        }
        
        $shop->save();
        
        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop updated successfully');
    }
    
    /**
     * Show all shops for a specific owner (shopowner view).
     *
     * @return \Illuminate\View\View
     */
    public function myShop()
    {
        $shops = Shop::where('shop_owner_id', Auth::id())->get();
        return view('shopowner.shops.my-shop', compact('shops'));
    }
    
    /**
     * Show the form for creating a new shop.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('shopowner.shops.create');
    }
    
    /**
     * Store a newly created shop in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'required|string',
            'shop_location' => 'required|string|max:255',
            'shop_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $shop = new Shop();
        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->shop_location = $request->shop_location;
        $shop->shop_owner_id = Auth::id();
        $shop->status = 'pending'; // Default status is pending for new shops
        
        // Handle image upload
        if ($request->hasFile('shop_image')) {
            $imagePath = $request->file('shop_image')->store('shops', 'public');
            $shop->shop_image = $imagePath;
        }
        
        $shop->save();
        
        return redirect()->route('shopowner.shops.my-shop')
            ->with('success', 'Shop created successfully and awaiting approval');
    }
    
    /**
     * Show the shop edit form for shop owner.
     *
     * @param int $shop_id
     * @return \Illuminate\View\View
     */
    public function ownerEdit($shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)
            ->where('shop_owner_id', Auth::id())
            ->firstOrFail();
            
        return view('shopowner.shops.edit', compact('shop'));
    }
    
    /**
     * Update the shop by shop owner.
     *
     * @param Request $request
     * @param int $shop_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ownerUpdate(Request $request, $shop_id)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'required|string',
            'shop_location' => 'required|string|max:255',
            'shop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $shop = Shop::where('shop_id', $shop_id)
            ->where('shop_owner_id', Auth::id())
            ->firstOrFail();
        
        $shop->shop_name = $request->shop_name;
        $shop->shop_description = $request->shop_description;
        $shop->shop_location = $request->shop_location;
        
        // Handle image upload if provided
        if ($request->hasFile('shop_image')) {
            // Delete old image if exists
            if ($shop->shop_image && Storage::exists('public/' . $shop->shop_image)) {
                Storage::delete('public/' . $shop->shop_image);
            }
            
            $imagePath = $request->file('shop_image')->store('shops', 'public');
            $shop->shop_image = $imagePath;
        }
        
        $shop->save();
        
        return redirect()->route('shopowner.shops.my-shop')
            ->with('success', 'Shop updated successfully');
    }
    
    /**
     * Display the specified shop.
     *
     * @param int $shop_id
     * @return \Illuminate\View\View
     */
    public function show($shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)
            ->where('status', 'active')
            ->firstOrFail();
            
        // Get shop products or other related data
        $products = $shop->products()->where('status', 'active')->get();
        
        return view('shops.show', compact('shop', 'products'));
    }
    
    /**
     * Get all active shops (public API).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveShops()
    {
        $shops = Shop::where('status', 'active')->get();
        return response()->json($shops);
    }
    
    /**
     * Get shops by location (public API).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShopsByLocation(Request $request)
    {
        $location = $request->input('location');
        $shops = Shop::where('status', 'active')
            ->where('shop_location', 'like', "%{$location}%")
            ->get();
            
        return response()->json($shops);
    }
    
    /**
     * Get shop statistics for admin dashboard.
     *
     * @return array
     */
    public function getShopStats()
    {
        $totalShops = Shop::count();
        $activeShops = Shop::where('status', 'active')->count();
        $pendingShops = Shop::where('status', 'pending')->count();
        $inactiveShops = Shop::where('status', 'inactive')->count();
        
        return [
            'total' => $totalShops,
            'active' => $activeShops,
            'pending' => $pendingShops,
            'inactive' => $inactiveShops
        ];
    }
}

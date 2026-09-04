<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutfitCategory;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // เพิ่มการนับจำนวนชุดในแต่ละหมวดหมู่
        $categories = OutfitCategory::select('OutfitCategories.*')
            ->leftJoin('ThaiOutfitCategories', 'OutfitCategories.category_id', '=', 'ThaiOutfitCategories.category_id')
            ->groupBy('OutfitCategories.category_id')
            ->selectRaw('COUNT(ThaiOutfitCategories.outfit_id) as outfits_count')
            ->orderBy('category_name')
            ->paginate(10);
        
        // Determine which view to use based on user type
        if (Auth::user()->userType == 'admin') {
            return view('admin.categories.index', compact('categories'));
        } else {
            return view('shopowner.categories.index', compact('categories'));
        }
    }
    
    public function create()
    {
        // Determine which view to use based on user type
        if (Auth::user()->userType == 'admin') {
            return view('admin.categories.create');
        } else {
            return view('shopowner.categories.create');
        }
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:OutfitCategories',
        ]);
        
        // Only validated fields should be used in create
        OutfitCategory::create($validated);
        
        $redirectRoute = Auth::user()->userType == 'admin' 
            ? 'admin.categories.index' 
            : 'shopowner.categories.index';
            
        return redirect()->route($redirectRoute)
            ->with('success', 'หมวดหมู่ถูกเพิ่มเรียบร้อยแล้ว');
    }
    
    public function edit($id)
    {
        $category = OutfitCategory::findOrFail($id);
        
        // Determine which view to use based on user type
        if (Auth::user()->userType == 'admin') {
            return view('admin.categories.edit', compact('category'));
        } else {
            return view('shopowner.categories.edit', compact('category'));
        }
    }
    
    public function update(Request $request, $id)
    {
        $category = OutfitCategory::findOrFail($id);
        
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:OutfitCategories,category_name,'.$id.',category_id',
        ]);
        
        $category->update($validated);
        
        $redirectRoute = Auth::user()->userType == 'admin' 
            ? 'admin.categories.index' 
            : 'shopowner.categories.index';
            
        return redirect()->route($redirectRoute)
            ->with('success', 'หมวดหมู่ถูกอัปเดตเรียบร้อยแล้ว');
    }
    
    public function destroy($id)
    {
        $category = OutfitCategory::findOrFail($id);
        
        // Check if category is in use
        $inUseCount = \App\Models\ThaiOutfitCategory::where('category_id', $id)->count();
        
        if ($inUseCount > 0) {
            $redirectRoute = Auth::user()->userType == 'admin' 
                ? 'admin.categories.index' 
                : 'shopowner.categories.index';
                
            return redirect()->route($redirectRoute)
                ->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากมีชุดที่ใช้หมวดหมู่นี้อยู่');
        }
        
        $category->delete();
        
        $redirectRoute = Auth::user()->userType == 'admin' 
            ? 'admin.categories.index' 
            : 'shopowner.categories.index';
            
        return redirect()->route($redirectRoute)
            ->with('success', 'หมวดหมู่ถูกลบเรียบร้อยแล้ว');
    }
}

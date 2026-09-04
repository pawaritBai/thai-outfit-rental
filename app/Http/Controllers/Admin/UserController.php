<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = User::query();
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('username', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('userType', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('user_id', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply filters
        if ($request->has('userType')) {
            $users->whereIn('userType', $request->userType);
        }
        
        // Apply sorting
        if ($request->has('orderBy')) {
            $users->orderBy($request->orderBy, $request->direction ?? 'asc');
        } else {
            $users->orderBy('user_id', 'asc');
        }
        
        $users = $users->get();
        
        // If it's an AJAX request, return only the table partial
        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'));
        }
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'username' => 'required|string|max:50|unique:users',
            'userType' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->username,
            'userType' => $request->userType,
            'status' => 'active',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully');
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($user->user_id, 'user_id'),
        ],
        'phone' => 'required|string|max:15',
        'userType' => 'required|string',
        'status' => 'required|string|in:active,inactive',
    ]);
    
    $user->fill($validated);
    $user->save();
    
    // Check if request is AJAX
    if ($request->ajax() || $request->has('is_ajax')) {
        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }
    
    // Normal redirect for non-AJAX requests
    return redirect()->route('admin.dashboard')->with('success', 'User updated successfully');
}

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully');
    }

    /**
     * Toggle the status of the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->status = $request->status;
            $user->save();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => $user->status
                ]);
            }
            
            return redirect()->back()->with('success', 'User status updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating user status: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating user status');
        }
    }

    /**
     * Show the user acceptance queue.
     *
     * @return \Illuminate\View\View
     */
    public function acceptance()
    {
        $pendingUsers = User::where('status', 'pending')
                            ->orderBy('created_at', 'asc')
                            ->get();
        
        return view('admin.users.acceptance', compact('pendingUsers'));
    }

    /**
     * Approve a user from the acceptance queue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();
        
        return redirect()->route('admin.users.acceptance')
                         ->with('success', 'User approved successfully');
    }

    /**
     * Decline a user from the acceptance queue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'inactive';
        $user->save();
        
        return redirect()->route('admin.users.acceptance')
                         ->with('success', 'User declined successfully');
    }
}

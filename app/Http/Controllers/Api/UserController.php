<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::select(['id', 'username', 'name', 'email', 'role', 'is_active', 'created_at'])
            ->get()
            ->map(fn (User $user) => $this->formatUser($user));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'nullable|string|max:50|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager',
            'status' => 'nullable|in:active,inactive'
        ]);

        $validated['username'] = $validated['username'] ?? $this->generateUsernameFromEmail($validated['email']);
        $validated['password'] = Hash::make($validated['password']);
        $status = $validated['status'] ?? 'active';
        unset($validated['status']);
        $validated['is_active'] = $status === 'active';

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $this->formatUser($user)
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user) + [
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager',
            'status' => 'nullable|in:active,inactive'
        ]);

        // Hanya hash password jika disediakan
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (array_key_exists('status', $validated)) {
            $validated['is_active'] = $validated['status'] === 'active';
            unset($validated['status']);
        }

        if (empty($validated['username']) && !empty($validated['email'])) {
            $validated['username'] = $this->generateUsernameFromEmail($validated['email'], $user->id);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $this->formatUser($user->fresh())
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Assign or change user role
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:super_admin,finance_admin,sales_operator,ar_collector,auditor_viewer,manager'
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
            'data' => $this->formatUser($user)
        ]);
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->is_active ? 'inactive' : 'active';
        $user->update([
            'is_active' => $newStatus === 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "User status changed to {$newStatus}",
            'data' => $this->formatUser($user->fresh())
        ]);
    }

    private function formatUser(User $user): array
    {
        $status = $user->status ?? ($user->is_active ? 'active' : 'inactive');

        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $status,
            'is_active' => (bool) $user->is_active,
        ];
    }

    private function generateUsernameFromEmail(string $email, ?int $ignoreId = null): string
    {
        $base = Str::slug(Str::before($email, '@'), '');
        $base = $base !== '' ? $base : 'user';
        $candidate = Str::limit($base, 50, '');
        $i = 1;

        while (
            User::where('username', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix = (string) $i;
            $candidate = Str::limit($base, 50 - strlen($suffix), '') . $suffix;
            $i++;
        }

        return $candidate;
    }
}

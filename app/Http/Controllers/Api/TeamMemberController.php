<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index()
    {
        $TeamMember = TeamMember::all();
        
        return response()->json([
            'success' => true,
            'data' => $TeamMember
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $teamMembers = TeamMember::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $teamMembers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:team_members',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'status' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $TeamMember = TeamMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team Member created successfully',
            'data' => $TeamMember
        ], 201);
    }

    public function show(TeamMember $TeamMember)
    {
        return response()->json([
            'success' => true,
            'data' => $TeamMember
        ]);
    }

    public function update(Request $request, TeamMember $TeamMember)
    {
        $validated = $request->validate([
            'code' => 'required|unique:team_members,code,' . $TeamMember->id,
            'name' => 'required|string',
            'position' => 'nullable|string',
            'status' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $TeamMember->update($validated);
        $TeamMember->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Team Member updated successfully',
            'data' => $TeamMember,
            'was_changed' => $TeamMember->wasChanged(),
            'changes' => $TeamMember->getChanges()
        ]);
    }

    public function destroy($id)
{
    // search including soft-deleted record
    $teamMember = TeamMember::withTrashed()
        ->where('id', $id)
        ->orWhere('code', $id)
        ->first();

    if (! $teamMember) {
        return response()->json([
            'success' => false,
            'message' => 'Team Member not found',
            'query'  => $id
        ], 404);
    }

    try {
        // force delete permanently from database
        $teamMember->forceDelete();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to permanently delete Team Member',
            'error'   => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Team Member permanently deleted from database'
    ], 200);
}

}

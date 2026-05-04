<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TeamMember;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['productGroup'])->orderBy('code');

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->query('code') . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%')
                    ->orWhere('author_code', 'like', '%' . $q . '%')
                    ->orWhere('author_name', 'like', '%' . $q . '%');
            });
        }

        $data = $query->get()->map(fn ($product) => $this->transformProduct($product));

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required|string',
            'specification' => 'nullable|string',
            'description' => 'nullable|string',
            'author_code' => 'nullable|string',
            'author_name' => 'nullable|string',
            'compiler' => 'nullable|string',
            'year' => 'nullable|digits:4',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated = $this->resolveAuthorPayload($validated);
        $product = Product::create($validated);
        $product->load(['productGroup']);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $this->transformProduct($product)
        ], 201);
    }

    public function show(Product $product)
    {
        $product = Product::with(['productGroup'])->findOrFail($product->id);

        return response()->json([
            'success' => true,
            'data' => $this->transformProduct($product)
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required|string',
            'specification' => 'nullable|string',
            'description' => 'nullable|string',
            'author_code' => 'nullable|string',
            'author_name' => 'nullable|string',
            'compiler' => 'nullable|string',
            'year' => 'nullable|digits:4',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated = $this->resolveAuthorPayload($validated);
        $product->update($validated);
        $product->refresh();
        $product->load(['productGroup']);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $this->transformProduct($product),
            'was_changed' => $product->wasChanged(),
            'changes' => $product->getChanges()
        ]);
    }

   public function destroy($id)
{
    // Cari data termasuk yang sudah soft delete
    $product = Product::withTrashed()
        ->where('id', $id)
        ->orWhere('code', $id)
        ->first();

    if (! $product) {
        return response()->json([
            'success' => false,
            'message' => 'Product not found',
            'query' => $id
        ], 404);
    }

    try {
        // Hapus permanen dari database
        $product->forceDelete();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to permanently delete product',
            'error' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Product permanently deleted from database'
    ], 200);
}

    private function resolveAuthorPayload(array $validated): array
    {
        if (!empty($validated['author_code'])) {
            $member = TeamMember::withTrashed()->where('code', $validated['author_code'])->first();
            if (!$member) {
                throw ValidationException::withMessages([
                    'author_code' => ['Author code not found in team_members.'],
                ]);
            }

            $validated['author_code'] = $member->code;
            $validated['author_name'] = $member->name;
        }

        return $validated;
    }

    private function transformProduct(Product $product): array
    {
        $arr = $product->toArray();
        $arr['product_group'] = $product->productGroup ? [
            'id' => $product->productGroup->id,
            'code' => $product->productGroup->code,
            'name' => $product->productGroup->name,
        ] : null;
        $arr['author'] = [
            'code' => $arr['author_code'] ?? null,
            'name' => $arr['author_name'] ?? null,
        ];

        unset($arr['productGroup']);
        unset($arr['author_id']);

        return $arr;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display the hierarchical categories management page
     */
    public function index()
    {
        $categories = Cat::with([
            'user:id,name',
            'childrenRecursive.user:id,name',
            'titleTranslation'
        ])->whereNull('parent_id')
            ->orderBy('ord', 'asc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Get hierarchical categories for dropdown
     */
    public function getHierarchicalCategories()
    {
        $categories = Cat::with(['titleTranslation', 'childrenRecursive.titleTranslation'])
            ->whereNull('parent_id')
            ->orderBy('ord', 'asc')
            ->get();

        $hierarchical = $this->buildHierarchicalArray($categories);
        
        return response()->json($hierarchical);
    }

    /**
     * Store a new category
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cats,id',
            'ord' => 'required|integer|min:1',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $category = Cat::create([
                'parent_id' => $request->parent_id,
                'ord' => $request->ord,
                'status' => $request->status,
                'user_id' => auth()->id() ?? 1,
            ]);

            // Create translation
            $category->translations()->create([
                'title' => $request->title,
                'locale' => app()->getLocale() ?: 'en'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category->load(['titleTranslation', 'user:id,name'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category order and parent
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:cats,id',
            'parent_id' => 'nullable|exists:cats,id',
            'ord' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $category = Cat::findOrFail($request->id);
            
            // Check for circular reference
            if ($request->parent_id && $this->wouldCreateCircularReference($category->id, $request->parent_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move category: would create circular reference'
                ], 422);
            }

            $category->update([
                'parent_id' => $request->parent_id,
                'ord' => $request->ord
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category details
     */
    public function update(Request $request, Cat $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cats,id',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Check for circular reference
            if ($request->parent_id && $this->wouldCreateCircularReference($category->id, $request->parent_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update category: would create circular reference'
                ], 422);
            }

            $category->update([
                'parent_id' => $request->parent_id,
                'status' => $request->status
            ]);

            // Update translation
            $category->translations()->updateOrCreate(
                ['locale' => app()->getLocale() ?: 'en'],
                ['title' => $request->title]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category with confirmation
     */
    public function destroy(Request $request, Cat $category): JsonResponse
    {
        $childrenCount = $category->childrenRecursive()->count();
        
        if ($childrenCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "This category contains {$childrenCount} subcategories. Do you want to continue?",
                'children_count' => $childrenCount,
                'requires_confirmation' => true
            ]);
        }

        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:cats,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $totalChildren = 0;
        foreach ($request->category_ids as $categoryId) {
            $category = Cat::find($categoryId);
            $totalChildren += $category->childrenRecursive()->count();
        }

        if ($totalChildren > 0) {
            return response()->json([
                'success' => false,
                'message' => "Selected categories contain {$totalChildren} subcategories. Do you want to continue?",
                'children_count' => $totalChildren,
                'requires_confirmation' => true
            ]);
        }

        try {
            Cat::whereIn('id', $request->category_ids)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Categories deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk move categories to new parent
     */
    public function bulkMove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:cats,id',
            'new_parent_id' => 'nullable|exists:cats,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->category_ids as $categoryId) {
                $category = Cat::find($categoryId);
                
                // Check for circular reference
                if ($request->new_parent_id && $this->wouldCreateCircularReference($category->id, $request->new_parent_id)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot move categories: would create circular reference'
                    ], 422);
                }

                $category->update(['parent_id' => $request->new_parent_id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categories moved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error moving categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save expand/collapse state in session
     */
    public function saveExpandState(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'collapsed_categories' => 'array',
            'collapsed_categories.*' => 'integer',
            'expanded_categories' => 'array', // Keep for backward compatibility
            'expanded_categories.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use collapsed_categories if provided, otherwise use expanded_categories for backward compatibility
        if ($request->has('collapsed_categories')) {
            session(['collapsed_categories' => $request->collapsed_categories]);
        } elseif ($request->has('expanded_categories')) {
            session(['expanded_categories' => $request->expanded_categories]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expand state saved successfully'
        ]);
    }

    /**
     * Get expand/collapse state from session
     */
    public function getExpandState(): JsonResponse
    {
        $collapsedCategories = session('collapsed_categories', []);
        $expandedCategories = session('expanded_categories', []); // For backward compatibility
        
        return response()->json([
            'success' => true,
            'collapsed_categories' => $collapsedCategories,
            'expanded_categories' => $expandedCategories // Keep for backward compatibility
        ]);
    }

    /**
     * Reset expand/collapse state
     */
    public function resetExpandState(): JsonResponse
    {
        session()->forget(['expanded_categories', 'collapsed_categories']);

        return response()->json([
            'success' => true,
            'message' => 'Expand state reset successfully'
        ]);
    }

    /**
     * Search categories
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $status = $request->get('status');
        $level = $request->get('level');

        $categories = Cat::with(['titleTranslation', 'user:id,name', 'childrenRecursive'])
            ->whereHas('titleTranslation', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            });

        if ($status !== null) {
            $categories->where('status', $status);
        }

        if ($level !== null) {
            if ($level == 0) {
                $categories->whereNull('parent_id');
            } else {
                $categories->whereHas('parent', function ($q) use ($level) {
                    $this->addLevelFilter($q, $level - 1);
                });
            }
        }

        $results = $categories->get();

        return response()->json([
            'success' => true,
            'categories' => $results
        ]);
    }

    /**
     * Check if moving a category would create circular reference
     */
    private function wouldCreateCircularReference(int $categoryId, int $newParentId): bool
    {
        if ($categoryId === $newParentId) {
            return true;
        }

        $newParent = Cat::find($newParentId);
        if (!$newParent) {
            return false;
        }

        // Check if new parent is a descendant of the category being moved
        $descendants = $this->getAllDescendants($categoryId);
        return in_array($newParentId, $descendants);
    }

    /**
     * Get all descendants of a category
     */
    private function getAllDescendants(int $categoryId): array
    {
        $descendants = [];
        $children = Cat::where('parent_id', $categoryId)->get();

        foreach ($children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $this->getAllDescendants($child->id));
        }

        return $descendants;
    }

    /**
     * Build hierarchical array for dropdown
     */
    private function buildHierarchicalArray($categories, $level = 0): array
    {
        $result = [];
        $prefix = str_repeat('→ ', $level);

        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->id,
                'title' => $prefix . ($category->titleTranslation->title ?? 'Untitled'),
                'level' => $level
            ];

            if ($category->childrenRecursive->count() > 0) {
                $result = array_merge($result, $this->buildHierarchicalArray($category->childrenRecursive, $level + 1));
            }
        }

        return $result;
    }

    /**
     * Add level filter to query
     */
    private function addLevelFilter($query, $level)
    {
        if ($level == 0) {
            $query->whereNull('parent_id');
        } else {
            $query->whereHas('parent', function ($q) use ($level) {
                $this->addLevelFilter($q, $level - 1);
            });
        }
    }
}

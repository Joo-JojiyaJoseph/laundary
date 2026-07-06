<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryRequest;
use App\Models\ProductCategory;

/**
 * Livewire\Admin\Categories\Index -> CategoryController
 *   $search (wire:model)     -> ?search= query param
 *   save()                   -> store() / update()
 *   delete()                 -> destroy()
 */
class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = ProductCategory::withCount('products')
            ->when(request('search'), fn ($q) => $q->where('name', 'like', '%' . request('search') . '%'))
            ->orderBy('name')
            ->paginate((int) request('per_page', 12));

        return $this->ok($categories, 'Categories fetched successfully');
    }

    public function show(ProductCategory $category)
    {
        $category->loadCount('products');

        return $this->ok($category, 'Category fetched successfully');
    }

    public function store(CategoryRequest $request)
    {
        $category = ProductCategory::create($request->validated());

        return $this->created($category, 'Category created successfully');
    }

    public function update(CategoryRequest $request, ProductCategory $category)
    {
        $category->update($request->validated());

        return $this->ok($category, 'Category updated successfully');
    }

    public function destroy(ProductCategory $category)
    {
        $category->delete();

        return $this->ok(null, 'Category deleted successfully');
    }
}

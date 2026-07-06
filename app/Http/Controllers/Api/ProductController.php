<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;

/**
 * Livewire\Admin\Products\Index -> ProductController
 *   search/serviceFilter/categoryFilter (wire:model) -> query params
 *   "formServices" (services for the chosen form category) -> servicesForCategory()
 */
class ProductController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $products = Product::with(['service', 'category'])
            ->when(request('search'), fn ($q) => $q->where('name', 'like', '%' . request('search') . '%'))
            ->when(request('service_id'), fn ($q) => $q->where('service_id', request('service_id')))
            ->when(request('category_id'), fn ($q) => $q->where('product_category_id', request('category_id')))
            ->orderBy('priority')->orderBy('name')
            ->paginate((int) request('per_page', 15));

        return $this->ok($products, 'Products fetched successfully');
    }

    public function show(Product $product)
    {
        $product->load(['service', 'category']);

        return $this->ok($product, 'Product fetched successfully');
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create([
            'name' => $data['name'],
            'service_id' => $data['service_id'],
            'product_category_id' => $data['category_id'],
            'uom' => $data['uom'],
            'price' => $data['price'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $this->created($product, 'Product created successfully');
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->update([
            'name' => $data['name'],
            'service_id' => $data['service_id'],
            'product_category_id' => $data['category_id'],
            'uom' => $data['uom'],
            'price' => $data['price'],
            'is_active' => $data['is_active'] ?? $product->is_active,
        ]);

        return $this->ok($product, 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->ok(null, 'Product deleted successfully');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return $this->ok($product, 'Product status updated');
    }

    /** GET /categories/{category}/services — powers the cascading category -> service picker. */
    public function servicesForCategory(ProductCategory $category)
    {
        $services = Service::where('product_category_id', $category->id)->orderBy('name')->get(['id', 'name']);

        return $this->ok($services, 'Services fetched successfully');
    }
}

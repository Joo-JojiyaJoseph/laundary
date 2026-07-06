<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceRequest;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Support\Str;

/**
 * Livewire\Admin\Services\Index -> ServiceController
 *   The unique-slug loop inside save() is preserved verbatim in
 *   generateUniqueSlug() below.
 */
class ServiceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $services = Service::withCount('products')->with('category')
            ->when(request('search'), fn ($q) => $q->where('name', 'like', '%' . request('search') . '%'))
            ->orderBy('priority')->orderBy('name')
            ->paginate((int) request('per_page', 10));

        return $this->ok($services, 'Services fetched successfully');
    }

    public function show(Service $service)
    {
        $service->load('category')->loadCount('products');

        return $this->ok($service, 'Service fetched successfully');
    }

    public function store(ServiceRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['is_active'] = true;

        $service = Service::create($data);

        return $this->created($service, 'Service created successfully');
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['slug'] = $this->generateUniqueSlug($data['name'], $service->id);

        $service->update($data);

        return $this->ok($service, 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return $this->ok(null, 'Service deleted successfully');
    }

    /** services.slug has a UNIQUE index — append -2, -3, ... until free. */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Service::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . ++$i;
        }

        return $slug;
    }
}

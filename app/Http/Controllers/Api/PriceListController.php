<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PriceListRequest;
use App\Models\PriceList;

/**
 * Livewire\Admin\PriceLists\Index -> PriceListController
 *   typeFilter                -> ?type= query param
 *   customerSearch/"customers"-> reuse CustomerController::search() from the mobile client
 *   save()                    -> store()/update(), including the same
 *                                 branch_id/customer_id nulling rules per type.
 */
class PriceListController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $rules = PriceList::with(['product.service', 'branch', 'customer'])
            ->when(request('type'), fn ($q) => $q->where('type', request('type')))
            ->latest()
            ->paginate((int) request('per_page', 15));

        return $this->ok($rules, 'Price rules fetched successfully');
    }

    public function show(PriceList $priceList)
    {
        $priceList->load(['product.service', 'branch', 'customer']);

        return $this->ok($priceList, 'Price rule fetched successfully');
    }

    public function store(PriceListRequest $request)
    {
        $data = $this->normalize($request->validated());

        $priceList = PriceList::create($data);

        return $this->created($priceList, 'Price rule created successfully');
    }

    public function update(PriceListRequest $request, PriceList $priceList)
    {
        $data = $this->normalize($request->validated());

        $priceList->update($data);

        return $this->ok($priceList, 'Price rule updated successfully');
    }

    public function destroy(PriceList $priceList)
    {
        $priceList->delete();

        return $this->ok(null, 'Price rule deleted successfully');
    }

    public function toggle(PriceList $priceList)
    {
        $priceList->update(['is_active' => ! $priceList->is_active]);

        return $this->ok($priceList, 'Price rule status updated');
    }

    /** Same branch_id/customer_id nulling the Livewire save() does based on "type". */
    protected function normalize(array $data): array
    {
        if ($data['type'] !== 'customer') {
            $data['customer_id'] = null;
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomerRequest;
use App\Http\Requests\Api\UpdateCustomerRequest;
use App\Models\Customer;

/**
 * Livewire\Admin\Customers\Index + Show -> CustomerController
 *   search/tierFilter/period/dateFrom/dateTo (wire:model) -> query params,
 *     see Concerns\AppliesDateFilter for the period/dateFrom/dateTo mapping.
 *   save()      -> store() / update()
 *   delete()    -> destroy()
 *   Show::render() (orders/payments/totals for one customer) -> summary()
 */
class CustomerController extends Controller
{
    use ApiResponse;
    use \App\Http\Controllers\Api\Concerns\AppliesDateFilter;

    public function index()
    {
        $customers = Customer::withCount('orders')
            ->when(request('search'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . request('search') . '%')
                ->orWhere('mobile', 'like', '%' . request('search') . '%')
                ->orWhere('code', 'like', '%' . request('search') . '%')))
            ->when(request('tier'), fn ($q) => $q->where('loyalty_tier', request('tier')))
            ->tap(fn ($q) => $this->applyDateFilter($q))
            ->latest()
            ->paginate((int) request('per_page', 12));

        return $this->ok($customers, 'Customers fetched successfully');
    }

    /** GET /customers/search?q= — quick lookahead used by POS and Price-list forms. */
    public function search()
    {
        $term = (string) request('q', '');

        if (mb_strlen($term) < 2) {
            return $this->ok([], 'Enter at least 2 characters to search');
        }

        $customers = Customer::where('name', 'like', "%{$term}%")
            ->orWhere('mobile', 'like', "%{$term}%")
            ->take((int) request('limit', 8))
            ->get(['id', 'name', 'mobile']);

        return $this->ok($customers, 'Customers fetched successfully');
    }

    public function show(Customer $customer)
    {
        $customer->loadCount('orders');

        return $this->ok($customer, 'Customer fetched successfully');
    }

    /** GET /customers/{customer}/summary — orders, payments and totals shown on Admin\Customers\Show. */
    public function summary(Customer $customer)
    {
        $orders = $customer->orders()->with('branch')->latest()->take(50)->get();
        $payments = $customer->payments()->with('order')->latest()->take(50)->get();

        $totals = [
            'orders' => $customer->orders()->count(),
            'spent' => (float) $customer->orders()->sum('total'),
            'paid' => (float) $customer->payments()->where('type', 'payment')->sum('amount'),
            'outstanding' => (float) $customer->orders()
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as due')
                ->value('due'),
        ];

        return $this->ok(compact('orders', 'payments', 'totals'), 'Customer summary fetched successfully');
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        $data['branch_id'] = $data['branch_id'] ?? null;

        $customer = Customer::create($data);

        return $this->created($customer, 'Customer created successfully');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $data['branch_id'] = $data['branch_id'] ?? null;

        $customer->update($data);

        return $this->ok($customer, 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return $this->ok(null, 'Customer removed successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BranchRequest;
use App\Models\Branch;
use Illuminate\Support\Str;

/**
 * Livewire\Admin\Branches\Index -> BranchController
 *   $showModal/create()/edit()  -> not needed (client renders its own form)
 *   save()                      -> store() / update()
 *   toggle()                    -> toggle()
 *   render()'s "branches" query -> index()
 */
class BranchController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $branches = Branch::withCount(['orders', 'customers'])
            ->orderBy('name')
            ->paginate((int) request('per_page', 10));

        return $this->ok($branches, 'Branches fetched successfully');
    }

    public function show(Branch $branch)
    {
        $branch->loadCount(['orders', 'customers']);

        return $this->ok($branch, 'Branch fetched successfully');
    }

    public function store(BranchRequest $request)
    {
        $data = $request->validated();
        $data['code'] = 'BR-' . strtoupper(Str::random(4));

        $branch = Branch::create($data);

        return $this->created($branch, 'Branch created successfully');
    }

    public function update(BranchRequest $request, Branch $branch)
    {
        $branch->update($request->validated());

        return $this->ok($branch, 'Branch updated successfully');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return $this->ok(null, 'Branch deleted successfully');
    }

    /** POST /branches/{branch}/toggle — flips is_active, same as Livewire's toggle(). */
    public function toggle(Branch $branch)
    {
        $branch->update(['is_active' => ! $branch->is_active]);

        return $this->ok($branch, 'Branch status updated');
    }
}

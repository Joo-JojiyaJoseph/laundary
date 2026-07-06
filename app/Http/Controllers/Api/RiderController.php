<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRiderRequest;
use App\Http\Requests\Api\UpdateRiderRequest;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Livewire\Admin\Riders\Index -> RiderController
 *   save() (create branch)  -> store() — creates the User + assigns the "rider" role + Rider row
 *   save() (edit branch)    -> update() — updates the underlying User, password optional
 */
class RiderController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $riders = Rider::with(['user', 'branch'])->withCount('orders')->latest()->paginate((int) request('per_page', 12));

        return $this->ok($riders, 'Riders fetched successfully');
    }

    public function show(Rider $rider)
    {
        $rider->load(['user', 'branch'])->loadCount('orders');

        return $this->ok($rider, 'Rider fetched successfully');
    }

    public function store(StoreRiderRequest $request)
    {
        $data = $request->validated();

        try {
            $rider = DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'branch_id' => $data['branch_id'] ?? null,
                ]);
                $user->assignRole('rider');

                return Rider::create([
                    'user_id' => $user->id,
                    'branch_id' => $data['branch_id'] ?? null,
                    'vehicle_number' => $data['vehicle_number'] ?? null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->fail('This rider could not be saved — the email may already be in use.', 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Something went wrong saving this rider. Please try again.', 500);
        }

        $rider->load(['user', 'branch']);

        return $this->created($rider, 'Rider saved.');
    }

    public function update(UpdateRiderRequest $request, Rider $rider)
    {
        $data = $request->validated();
        $rider->loadMissing('user');

        try {
            DB::transaction(function () use ($rider, $data) {
                $rider->user->update(array_filter([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => ! empty($data['password']) ? Hash::make($data['password']) : null,
                ]));

                $rider->update([
                    'vehicle_number' => $data['vehicle_number'] ?? null,
                    'branch_id' => $data['branch_id'] ?? null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->fail('This rider could not be saved — the email may already be in use.', 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->fail('Something went wrong saving this rider. Please try again.', 500);
        }

        $rider->load(['user', 'branch']);

        return $this->ok($rider, 'Rider saved.');
    }

    public function destroy(Rider $rider)
    {
        $rider->delete();

        return $this->ok(null, 'Rider deleted successfully');
    }
}

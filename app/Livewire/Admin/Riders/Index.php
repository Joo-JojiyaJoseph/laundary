<?php

namespace App\Livewire\Admin\Riders;

use App\Models\Branch;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = "";
    public string $email = "";
    public string $password = "";
    public string $vehicle_number = "";
    public $branch_id = null;

    public function create(): void
    {
        $this->reset(["editingId", "name", "email", "password", "vehicle_number"]);
        $this->branch_id = auth()->user()->branch_id;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $rider = Rider::with("user")->findOrFail($id);
        $this->editingId = $id;
        $this->name = $rider->user->name;
        $this->email = $rider->user->email;
        $this->password = "";
        $this->vehicle_number = (string) $rider->vehicle_number;
        $this->branch_id = $rider->branch_id;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->branch_id = $this->branch_id ?: null;
        $rider = $this->editingId ? Rider::with("user")->findOrFail($this->editingId) : null;

        $data = $this->validate([
            "name" => "required|string|max:120",
            "email" => "required|email|max:150|unique:users,email" . ($rider ? "," . $rider->user_id : ""),
            "password" => ($rider ? "nullable" : "required") . "|string|min:8",
            "vehicle_number" => "nullable|string|max:20",
            "branch_id" => "nullable|exists:branches,id",
        ]);

        DB::transaction(function () use ($rider, $data) {
            if ($rider) {
                $rider->user->update(array_filter([
                    "name" => $data["name"],
                    "email" => $data["email"],
                    "password" => $data["password"] ? Hash::make($data["password"]) : null,
                ]));
                $rider->update(["vehicle_number" => $data["vehicle_number"], "branch_id" => $data["branch_id"]]);
            } else {
                $user = User::create([
                    "name" => $data["name"],
                    "email" => $data["email"],
                    "password" => Hash::make($data["password"]),
                    "branch_id" => $data["branch_id"],
                ]);
                $user->assignRole("rider");
                Rider::create([
                    "user_id" => $user->id,
                    "branch_id" => $data["branch_id"],
                    "vehicle_number" => $data["vehicle_number"],
                ]);
            }
        });

        $this->showModal = false;
        $this->dispatch("notify", type: "success", message: "Rider saved.");
    }

    public function render()
    {
        return view("livewire.admin.riders.index", [
            "riders" => Rider::with(["user", "branch"])->withCount("orders")->latest()->paginate(12),
            "branches" => Branch::orderBy("name")->get(["id", "name"]),
        ])->layout("layouts.admin", ["title" => "Riders"]);
    }
}

<?php

namespace App\Livewire\Admin\Branches;

use App\Models\Branch;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = "";
    public string $phone = "";
    public string $email = "";
    public string $address = "";
    public string $city = "";
    public string $pincode = "";
    public bool $is_active = true;

    public function create(): void
    {
        $this->reset(["editingId", "name", "phone", "email", "address", "city", "pincode"]);
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $branch = Branch::findOrFail($id);
        $this->editingId = $id;
        $this->name = $branch->name;
        $this->phone = (string) $branch->phone;
        $this->email = (string) $branch->email;
        $this->address = (string) $branch->address;
        $this->city = (string) $branch->city;
        $this->pincode = (string) $branch->pincode;
        $this->is_active = (bool) $branch->is_active;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            "name" => "required|string|max:120",
            "phone" => "nullable|string|max:20",
            "email" => "nullable|email|max:150",
            "address" => "nullable|string|max:500",
            "city" => "nullable|string|max:80",
            "pincode" => "nullable|string|max:10",
            "is_active" => "boolean",
        ]);

        if (! $this->editingId) {
            $data["code"] = "BR-" . strtoupper(Str::random(4));
        }

        Branch::updateOrCreate(["id" => $this->editingId], $data);
        $this->showModal = false;
        $this->dispatch("notify", type: "success", message: "Branch saved.");
    }

    public function toggle(int $id): void
    {
        $branch = Branch::findOrFail($id);
        $branch->update(["is_active" => ! $branch->is_active]);
    }

    public function render()
    {
        return view("livewire.admin.branches.index", [
            "branches" => Branch::withCount(["orders", "customers"])->orderBy("name")->paginate(10),
        ])->layout("layouts.admin", ["title" => "Branches"]);
    }
}

<?php

namespace App\Livewire\Admin\Customers;

use App\Livewire\Concerns\WithDateFilter;
use App\Models\Branch;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithDateFilter;

    public string $search = "";
    public string $tierFilter = "";

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = "";
    public string $mobile = "";
    public $alternate_mobile = "";
    public $email = "";
    public $birthday = null;
    public $address = "";
    public $city = "";
    public $pincode = "";
    public $branch_id = null;
    public $notes = "";
    public bool $is_vip = false;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedTierFilter(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(["editingId", "name", "mobile", "alternate_mobile", "email", "address", "birthday", "city", "pincode", "branch_id", "notes"]);
        $this->is_vip = false;
        $this->branch_id = auth()->user()->branch_id;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->fill($customer->only(["name", "mobile", "branch_id"]));
        $this->alternate_mobile = (string) $customer->alternate_mobile;
        $this->email = (string) $customer->email;
        $this->birthday = $customer->birthday?->toDateString();
        $this->address = (string) $customer->address;
        $this->city = (string) $customer->city;
        $this->pincode = (string) $customer->pincode;
        $this->notes = (string) $customer->notes;
        $this->is_vip = (bool) $customer->is_vip;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->branch_id = $this->branch_id ?: null;
        foreach (["alternate_mobile", "email", "address", "birthday", "city", "pincode", "notes"] as $field) {
            $this->$field = trim((string) $this->$field) === "" ? null : $this->$field;
        }
        $data = $this->validate([
            "name" => "required|string|max:120",
            "mobile" => "required|string|max:20",
            "alternate_mobile" => "nullable|string|max:20",
            "email" => "nullable|email|max:150",
            "address" => "nullable|string|max:500",
            "birthday" => "nullable|date|before:today",
            "city" => "nullable|string|max:80",
            "pincode" => "nullable|string|max:10",
            "branch_id" => "nullable|exists:branches,id",
            "notes" => "nullable|string|max:2000",
            "is_vip" => "boolean",
        ]);

        Customer::updateOrCreate(["id" => $this->editingId], $data);
        $this->showModal = false;
        $this->dispatch("notify", type: "success", message: "Customer saved.");
    }

    public function delete(int $id): void
    {
        Customer::findOrFail($id)->delete();
        $this->dispatch("notify", type: "success", message: "Customer removed.");
    }

    public function render()
    {
        return view("livewire.admin.customers.index", [
            "customers" => Customer::withCount("orders")
                ->when($this->search, fn ($q) => $q->where(fn ($w) => $w
                    ->where("name", "like", "%{$this->search}%")
                    ->orWhere("mobile", "like", "%{$this->search}%")
                    ->orWhere("code", "like", "%{$this->search}%")))
                ->when($this->tierFilter, fn ($q) => $q->where("loyalty_tier", $this->tierFilter))
                ->tap(fn ($q) => $this->applyDateFilter($q))
                ->latest()
                ->paginate(12),
            "branches" => Branch::orderBy("name")->get(["id", "name"]),
        ])->layout("layouts.admin", ["title" => "Customers"]);
    }
}

<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

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
        // Only the optional fields are nulled when blank — name, mobile and
        // address stay required and are validated below.
        foreach (["alternate_mobile", "email", "birthday", "city", "pincode", "notes"] as $field) {
            $this->$field = trim((string) $this->$field) === "" ? null : $this->$field;
        }

        $data = $this->validate([
            // Mandatory: name, mobile, address.
            "name" => "required|string|max:120",
            "mobile" => [
                "required", "string", "max:20", "regex:/^[0-9+\-\s]{8,20}$/",
                Rule::unique("customers", "mobile")->ignore($this->editingId),
            ],
            "address" => "required|string|max:500",
            // Optional everything else.
            "alternate_mobile" => "nullable|string|max:20",
            "email" => [
                "nullable", "email", "max:150",
                Rule::unique("customers", "email")->ignore($this->editingId),
            ],
            "birthday" => "nullable|date|before:today",
            "city" => "nullable|string|max:80",
            "pincode" => "nullable|string|max:10",
            "branch_id" => "nullable|exists:branches,id",
            "notes" => "nullable|string|max:2000",
            "is_vip" => "boolean",
        ], [
            "mobile.unique" => "A customer with this mobile number already exists.",
            "mobile.regex" => "Please enter a valid mobile number.",
            "email.unique" => "A customer with this email already exists.",
            "address.required" => "Address is required.",
        ]);

        try {
            Customer::updateOrCreate(["id" => $this->editingId], $data);
        } catch (\Illuminate\Database\QueryException $e) {
            // Surface DB-level issues (e.g. unique constraints) on the form
            // instead of throwing a 500 error page.
            $this->addError("mobile", "This customer could not be saved — it may already exist.");
            return;
        }

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
                ->latest()
                ->paginate(12),
            "branches" => Branch::orderBy("name")->get(["id", "name"]),
        ])->layout("layouts.admin", ["title" => "Customers"]);
    }
}

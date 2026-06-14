<?php

namespace App\Livewire\Admin\PriceLists;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\PriceList;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $typeFilter = "";
    public bool $showModal = false;
    public ?int $editingId = null;

    public $product_id = null;
    public string $type = "branch";
    public $branch_id = null;
    public $customer_id = null;
    public ?string $price = null;
    public ?string $starts_at = null;
    public ?string $ends_at = null;
    public bool $is_active = true;

    public string $customerSearch = "";

    public function updatedTypeFilter(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(["editingId", "product_id", "branch_id", "customer_id", "price", "starts_at", "ends_at", "customerSearch"]);
        $this->type = "branch";
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $rule = PriceList::findOrFail($id);
        $this->editingId = $id;
        $this->product_id = $rule->product_id;
        $this->type = $rule->type;
        $this->branch_id = $rule->branch_id;
        $this->customer_id = $rule->customer_id;
        $this->price = (string) $rule->price;
        $this->starts_at = $rule->starts_at?->toDateString();
        $this->ends_at = $rule->ends_at?->toDateString();
        $this->is_active = (bool) $rule->is_active;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        foreach (["product_id", "branch_id", "customer_id"] as $f) $this->$f = $this->$f ?: null;
        $data = $this->validate([
            "product_id" => "required|exists:products,id",
            "type" => "required|in:branch,customer,vip,seasonal,promo",
            "branch_id" => "nullable|exists:branches,id|required_if:type,branch",
            "customer_id" => "nullable|exists:customers,id|required_if:type,customer",
            "price" => "required|numeric|min:0",
            "starts_at" => "nullable|date",
            "ends_at" => "nullable|date|after_or_equal:starts_at",
            "is_active" => "boolean",
        ]);

        if ($data["type"] !== "branch") $data["branch_id"] = $this->branch_id; // optional scoping
        if ($data["type"] !== "customer") $data["customer_id"] = null;

        PriceList::updateOrCreate(["id" => $this->editingId], $data);
        $this->showModal = false;
        $this->dispatch("notify", type: "success", message: "Price rule saved.");
    }

    public function toggle(int $id): void
    {
        $rule = PriceList::findOrFail($id);
        $rule->update(["is_active" => ! $rule->is_active]);
    }

    public function delete(int $id): void
    {
        PriceList::findOrFail($id)->delete();
        $this->dispatch("notify", type: "success", message: "Price rule deleted.");
    }

    public function render()
    {
        return view("livewire.admin.price-lists.index", [
            "rules" => PriceList::with(["product.service", "branch", "customer"])
                ->when($this->typeFilter, fn ($q) => $q->where("type", $this->typeFilter))
                ->latest()
                ->paginate(15),
            "products" => Product::with("service")->orderBy("name")->get(["id", "name", "service_id", "price"]),
            "branches" => Branch::orderBy("name")->get(["id", "name"]),
            "customers" => strlen($this->customerSearch) >= 2
                ? Customer::where("name", "like", "%{$this->customerSearch}%")
                    ->orWhere("mobile", "like", "%{$this->customerSearch}%")->take(8)->get(["id", "name", "mobile"])
                : collect(),
        ])->layout("layouts.admin", ["title" => "Price lists"]);
    }
}

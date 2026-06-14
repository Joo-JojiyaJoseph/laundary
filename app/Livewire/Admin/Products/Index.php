<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    #[\Livewire\Attributes\Url]
    public $serviceFilter = null;
    public $categoryFilter = null;

    public bool $showModal = false;
    public ?int $editingId = null;

    // Form: pick category -> service -> item details
    public $form_category_id = null;
    public $service_id = null;
    public string $name = "";
    public string $uom = "pc";
    public ?string $price = null;
    public bool $is_active = true;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedServiceFilter(): void { $this->resetPage(); }
    public function updatedCategoryFilter(): void { $this->resetPage(); }

    /** When the category changes in the form, reset the chosen service. */
    public function updatedFormCategoryId(): void
    {
        $this->service_id = null;
    }

    public function create(): void
    {
        $this->reset(["editingId", "name", "service_id", "form_category_id", "price"]);
        $this->uom = "pc";
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $product = Product::with("service")->findOrFail($id);
        $this->editingId = $id;
        $this->name = $product->name;
        $this->service_id = $product->service_id;
        // Derive the category from the service (fallback to the product's own category)
        $this->form_category_id = $product->service?->product_category_id ?? $product->product_category_id;
        $this->uom = $product->uom;
        $this->price = (string) $product->price;
        $this->is_active = (bool) $product->is_active;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->form_category_id = $this->form_category_id ?: null;
        $this->service_id = $this->service_id ?: null;

        $data = $this->validate([
            "form_category_id" => "required|exists:product_categories,id",
            "service_id" => "required|exists:services,id",
            "name" => "required|string|max:100",
            "uom" => "required|in:pc,kg,pair,set,mtr",
            "price" => "required|numeric|min:0",
            "is_active" => "boolean",
        ], [], [
            "form_category_id" => "category",
            "service_id" => "service",
        ]);

        Product::updateOrCreate(["id" => $this->editingId], [
            "name" => $data["name"],
            "service_id" => $data["service_id"],
            "product_category_id" => $data["form_category_id"],
            "uom" => $data["uom"],
            "price" => $data["price"],
            "is_active" => $data["is_active"],
        ]);

        $this->showModal = false;
        $this->dispatch("notify", type: "success", title: "Saved", message: "Item saved.");
    }

    public function toggle(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(["is_active" => ! $product->is_active]);
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
        $this->dispatch("notify", type: "success", message: "Item deleted.");
    }

    public function render()
    {
        return view("livewire.admin.products.index", [
            "products" => Product::with(["service", "category"])
                ->when($this->search, fn ($q) => $q->where("name", "like", "%{$this->search}%"))
                ->when($this->serviceFilter, fn ($q) => $q->where("service_id", $this->serviceFilter))
                ->when($this->categoryFilter, fn ($q) => $q->where("product_category_id", $this->categoryFilter))
                ->orderBy("priority")->orderBy("name")
                ->paginate(15),
            "services" => Service::orderBy("priority")->get(["id", "name", "product_category_id"]),
            "categories" => ProductCategory::orderBy("priority")->orderBy("name")->get(["id", "name"]),
            // Services available for the currently-selected form category
            "formServices" => $this->form_category_id
                ? Service::where("product_category_id", $this->form_category_id)->orderBy("name")->get(["id", "name"])
                : collect(),
        ])->layout("layouts.admin", ["title" => "Items & Pricing"]);
    }
}
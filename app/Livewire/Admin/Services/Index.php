<?php

namespace App\Livewire\Admin\Services;

use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = "";
    public $product_category_id = null;

    public function updatedSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(["editingId", "name", "product_category_id"]);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $service = Service::findOrFail($id);
        $this->editingId = $id;
        $this->name = $service->name;
        $this->product_category_id = $service->product_category_id;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->product_category_id = $this->product_category_id ?: null;

        $data = $this->validate([
            "name" => "required|string|max:100",
            "product_category_id" => "nullable|exists:product_categories,id",
        ]);

        // Build a unique slug (services.slug has a UNIQUE index)
        $base = Str::slug($data["name"]);
        $slug = $base;
        $i = 1;
        while (Service::where("slug", $slug)
            ->when($this->editingId, fn ($q) => $q->where("id", "!=", $this->editingId))
            ->exists()) {
            $slug = $base . "-" . ++$i;
        }

        try {
            Service::updateOrCreate(["id" => $this->editingId], [
                "name" => $data["name"],
                "product_category_id" => $data["product_category_id"],
                "slug" => $slug,
                "is_active" => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->addError("name", "This service could not be saved. Please try again.");
            return;
        }

        $this->showModal = false;
        $this->dispatch("notify", type: "success", title: "Saved", message: "Service saved.");
    }

    public function delete(int $id): void
    {
        Service::findOrFail($id)->delete();
        $this->dispatch("notify", type: "success", title: "Deleted", message: "Service deleted.");
    }

    public function render()
    {
        return view("livewire.admin.services.index", [
            "services" => Service::withCount("products")->with("category")
                ->when($this->search, fn ($q) => $q->where("name", "like", "%{$this->search}%"))
                ->orderBy("priority")->orderBy("name")
                ->paginate(10),
            "categories" => ProductCategory::orderBy("name")->get(["id", "name"]),
        ])->layout("layouts.admin", ["title" => "Services"]);
    }
}

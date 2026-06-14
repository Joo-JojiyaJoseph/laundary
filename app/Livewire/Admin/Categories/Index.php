<?php

namespace App\Livewire\Admin\Categories;

use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = "";

    public function updatedSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(["editingId", "name"]);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = ProductCategory::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            "name" => "required|string|max:100",
        ]);

        ProductCategory::updateOrCreate(["id" => $this->editingId], $data);
        $this->showModal = false;
        $this->dispatch("notify", type: "success", message: $this->editingId ? "Category updated." : "Category created.");
    }

    public function delete(int $id): void
    {
        ProductCategory::findOrFail($id)->delete();
        $this->dispatch("notify", type: "success", message: "Category deleted.");
    }

    public function render()
    {
        return view("livewire.admin.categories.index", [
            "categories" => ProductCategory::withCount("products")
                ->when($this->search, fn ($q) => $q->where("name", "like", "%{$this->search}%"))
                ->orderBy("name")
                ->paginate(12),
        ])->layout("layouts.admin", ["title" => "Categories"]);
    }
}

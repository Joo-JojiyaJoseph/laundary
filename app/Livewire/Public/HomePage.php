<?php

namespace App\Livewire\Public;

use App\Models\Service;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        return view("livewire.public.home-onepage", [
            "services" => Service::active()->take(6)->get(),
        ])->layout("layouts.public", ["title" => "Laundrix — Laundry, handled."]);
    }
}

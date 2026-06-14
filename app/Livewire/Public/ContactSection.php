<?php

namespace App\Livewire\Public;

use App\Models\ContactMessage;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactSection extends Component
{
    #[Validate("required|min:2|max:80")]
    public string $name = "";

    #[Validate("required|email")]
    public string $email = "";

    #[Validate("required|min:8|max:15")]
    public string $phone = "";

    #[Validate("required|min:10|max:2000")]
    public string $message = "";

    public function submit(): void
    {
        $this->validate();

        try {
            ContactMessage::create($this->only(["name", "email", "phone", "message"]));
            $this->reset(["name", "email", "phone", "message"]);
            $this->dispatch("notify", type: "success", title: "Message sent!", message: "Thanks for reaching out — we will reply within a few hours.");
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch("notify", type: "error", title: "Could not send", message: "Please try again or message us on WhatsApp.");
        }
    }

    public function render()
    {
        return view("livewire.public.contact-section");
    }
}

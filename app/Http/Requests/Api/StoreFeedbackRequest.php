<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/** Mirrors Livewire\Public\FeedbackSection (and the duplicate logic in Public\Home::ratingSubmit()). */
class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:80',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|min:10|max:1000',
        ];
    }
}

<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Base class for every API FormRequest. Guarantees a 422 JSON response on
 * validation failure and a 403 JSON response on authorization failure,
 * independent of the Accept header — so this works even if ForceJsonResponse
 * middleware were ever removed from the `api` group.
 *
 * All existing Api\* FormRequest classes should extend this instead of
 * Illuminate\Foundation\Http\FormRequest.
 */
abstract class ApiFormRequest extends FormRequest
{
    /**
     * Called automatically by the validation pipeline when rules fail.
     */
    protected function failedValidation(ValidatorContract $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Called if authorize() returns false — kept consistent with the same envelope.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
                'errors' => null,
            ], 403)
        );
    }
}
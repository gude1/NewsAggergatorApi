<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title="StoreUserPreferenceRequest",
 *     description="Store User Preference Request body",
 *     @OA\Property(property="category", type="string", description="User's preferred category", example="sports"),
 *     @OA\Property(property="author", type="string", description="User's preferred author", example="John Doe"),
 *     @OA\Property(property="source", type="string", description="User's preferred source", example="example.com")
 * )
 */
class StoreUserPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @OA\Schema()
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "catgory" => "bail|sometimes|required|min:3",
            "author" => "bail|sometimes|required|min:3",
            "source" => "bail|sometimes|required|min:3",
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title="StoreUserRequest",
 *     description="Store User Request body",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", description="User's name", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", description="User's email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", description="User's password", example="secretpassword")
 * )
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'bail|required|regex:/^[a-zA-Z ]*$/|between:7,50',
            "email" => "bail|required|email|between:10,50|unique:users",
            "password" => "bail|required|min:6"
        ];
    }
}
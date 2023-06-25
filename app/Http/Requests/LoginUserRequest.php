<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     title="LoginUserRequest",
 *     description="Login User Request body",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", description="User's email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", description="User's password", example="secretpassword")
 * )
 */
class LoginUserRequest extends FormRequest
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
            "email" => "bail|required|email",
            "password" => "bail|required"
        ];
    }
}
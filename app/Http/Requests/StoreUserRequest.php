<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {


        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'staff_number' => 'required|string|max:50|unique:users,staff_number',
            'role' => 'required|exists:roles,name',
            'division_id'   => 'nullable|required_if:role,Division Head|exists:divisions,id',
            'directorate_id' => 'nullable|required_if:role,Director|exists:directorates,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'staff_number.unique' => 'The staff number must be unique.',
//            'password.confirmed' => 'Passwords do not match.',
            'role.exists' => 'Selected role does not exist.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone' => 'required|string|max:20',
            'staff_number' => 'required|string|max:50',
            'role' => 'required|string|exists:roles,name',
            'division_id'   => 'nullable|required_if:role,Division Head|exists:divisions,id',
            'directorate_id' => 'nullable|required_if:role,Director|exists:directorates,id',
        ];
    }
}

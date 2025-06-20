<?php

namespace App\Http\Requests;

use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class UserGroupFormRequest extends FormRequest
{
    use TrimsInputTrait;

    /**
     * Prepare the data for validation.
     *
     * This method is called before validation occurs.
     * It allows us to modify the input data, such as trimming whitespace.
     */
    protected function prepareForValidation()
    {
        $this->merge($this->trimInputs($this->all()));
    }

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id', // Each permission should be a distinct string
        ];
    }
}

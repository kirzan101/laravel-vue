<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\UniqueIgnoringSoftDeletes;
use App\Traits\TrimsInputTrait;
use Illuminate\Foundation\Http\FormRequest;

class ProfileFormRequest extends FormRequest
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
            'username' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes(User::class, 'username', $this->user_id)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                new UniqueIgnoringSoftDeletes(User::class, 'email', $this->user_id)
            ],
            'status' => 'required|in:active,inactive',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'type' => 'required|in:user,admin',
            'contact_numbers' => 'nullable|array',
            'contact_numbers.*' => 'nullable|string|max:20', // Each contact number should be a string with a max length
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional avatar image
        ];
    }
}

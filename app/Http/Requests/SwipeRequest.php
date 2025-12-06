<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SwipeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:like,dislike'],
        ];
    }

    public function messages(): array
    {
        return [
            'target_user_id.exists' => 'The target user id does not exist.',
            'type.in' => 'The swipe action must be either "like" or "dislike".',
        ];
    }
}

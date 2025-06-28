<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SendMessageRequest
 * 
 * Validates incoming chat message requests
 */
class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users for now - can be modified for authentication
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:1',
                'max:1000',
                'regex:/^(?!\s*$).+/', // Not just whitespace
            ],
            'channel' => [
                'sometimes',
                'string',
                'min:1',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/', // Alphanumeric, underscore, hyphen only
            ],
            'user_name' => [
                'sometimes',
                'string',
                'min:1',
                'max:50',
            ],
            'user_id' => [
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Message content is required.',
            'content.min' => 'Message cannot be empty.',
            'content.max' => 'Message cannot exceed 1000 characters.',
            'content.regex' => 'Message cannot contain only whitespace.',
            'channel.regex' => 'Channel name can only contain letters, numbers, underscores, and hyphens.',
            'channel.max' => 'Channel name cannot exceed 50 characters.',
            'user_name.max' => 'Username cannot exceed 50 characters.',
        ];
    }

    /**
     * Get validated data with defaults
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        
        return [
            'content' => trim($validated['content']),
            'channel' => $validated['channel'] ?? 'general',
            'user_name' => $validated['user_name'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ];
    }
}

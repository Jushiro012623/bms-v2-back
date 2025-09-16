<?php

namespace App\Http\Requests\DocumentRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
            'doc_type_id' => ['required', 'exists:document_types,id'],
            'purpose' => ['sometimes'],
            'status' => ['required', 'in:1,2,3,4'],
            'release_date' => ['sometimes'],
            'notes' => ['nullable'],
        ];
    }
}

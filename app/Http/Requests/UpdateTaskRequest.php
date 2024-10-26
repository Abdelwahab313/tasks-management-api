<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:tasks,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => ['sometimes', Rule::in(Task::getStatuses())],
            'due_date' => 'sometimes|date|after:today',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->query('id')
        ]);
    }
}

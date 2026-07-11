<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexTasksRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:' . TaskStatusEnum::valuesString(),
            'search' => 'nullable|string|max:255',
            'due_date_from' => 'nullable|date',
            'due_date_to' => 'nullable|date',
            'sort_by' => 'nullable|string|in:id,title,status,due_date,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Статус может быть только pending, in_progress или completed.',
            'search.max' => 'Поисковый запрос не должен превышать :max символов.',
            'due_date_from.date' => 'Дата начала должна быть корректной датой.',
            'due_date_to.date' => 'Дата окончания должна быть корректной датой.',
            'sort_by.in' => 'Поле сортировки может быть только id, title, status, due_date или created_at.',
            'sort_direction.in' => 'Направление сортировки может быть только asc или desc.',
            'per_page.integer' => 'Количество элементов на странице должно быть числом.',
            'per_page.min' => 'Количество элементов на странице должно быть не менее :min.',
            'per_page.max' => 'Количество элементов на странице не должно превышать :max.'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Приводим параметры к нужному типу
        $this->merge([
            'per_page' => $this->input('per_page') ? (int) $this->input('per_page') : null,
            'sort_by' => $this->input('sort_by') ?: 'created_at',
            'sort_direction' => $this->input('sort_direction') ?: 'desc'
        ]);
    }
}

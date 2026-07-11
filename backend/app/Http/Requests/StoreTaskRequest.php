<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // проверка прав будет в контроллере
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:' . TaskStatusEnum::valuesString()
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Заголовок задачи обязателен.',
            'title.min' => 'Заголовок должен содержать минимум :min символа.',
            'title.max' => 'Заголовок не должен превышать :max символов.',
            'due_date.date' => 'Дата дедлайна должна быть корректной датой.',
            'status.in' => 'Статус может быть только pending, in_progress или completed.'
        ];
    }
}

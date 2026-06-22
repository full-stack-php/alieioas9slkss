<?php

namespace Modules\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveExportRequest extends FormRequest
{
    /**
     * Определяем, авторизован ли пользователь делать этот запрос.
     * Обычно здесь true, если проверка прав идет в middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'entity'        => ['required', 'string', 'max:255'],
            'format'        => ['required', Rule::in(['csv', 'xlsx', 'json', 'xml'])],
            'is_active'     => 'required',
            'cron_schedule' => ['nullable', 'string', 'max:100'],

            'settings'                => ['nullable', 'array'],
            'settings.xml_template'   => ['required_if:format,xml', 'nullable', 'string'],
            'settings.xml_root'       => ['nullable', 'string', 'max:50'],
            'settings.delimiter'      => ['nullable', 'string', 'max:5'],

            'columns'                 => ['required', 'array', 'min:1'],
            'columns.*.column'        => ['required', 'string', 'max:255'],
            'columns.*.field'         => ['required', 'string', 'max:255'],
            'columns.*.enabled'       => ['boolean'],
            'columns.*.type'          => ['nullable', Rule::in(['field', 'relation', 'callback'])],
            'columns.*.callback_class'=> ['required_if:columns.*.type,callback', 'nullable', 'string'],

            'filters'                 => ['nullable', 'array'],
            'filters.*.field'         => ['required_with:filters', 'string', 'max:255'],
            'filters.*.operator'      => ['required_with:filters', Rule::in(['=', '!=', 'IN', 'NOT IN', 'LIKE', 'BETWEEN', 'NULL', 'NOT NULL'])],
            'filters.*.value'         => ['nullable'],

            'sortings'                => ['nullable', 'array'],
            'sortings.*.field'        => ['required_with:sortings', 'string', 'max:255'],
            'sortings.*.direction'    => ['required_with:sortings', Rule::in(['asc', 'desc', 'ASC', 'DESC'])],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->get('is_active') === 'on' : false,
            'filters' => $this->input('filters', []),
            'columns' => $this->input('columns', []),
        ]);
    }


    public function messages(): array
    {
        return [
            'format.in' => 'Выбранный формат не поддерживается. Доступные форматы: csv, xlsx, json, xml.',
            'settings.xml_template.required_if' => 'Шаблон XML обязателен, если выбран формат XML.',
            'columns.min' => 'Необходимо выбрать хотя бы одну колонку для экспорта.',
            'columns.*.field.required' => 'Техническое поле в БД обязательно для каждой колонки.',
            'columns.*.callback_class.required_if' => 'Если выбран тип колонки "callback", необходимо указать класс обработчика.',
        ];
    }
}

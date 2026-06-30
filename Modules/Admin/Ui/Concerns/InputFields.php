<?php

namespace Modules\Admin\Ui\Concerns;

use LogicException;
use Modules\Support\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InputFields
{
    protected function inputField($name, $value, $class, $attributes, $options)
    {
        $readonly = array_pull($options, 'readonly', false);
        $disabled = array_get($options, 'disabled', false);

        return "<input
            name='{$name}'
            class='form-control {$class}'
            id='{$name}'
            value='{$value}'
            {$attributes}"
            . ($disabled ? 'disabled' : '')
            . ($readonly ? 'readonly ' : '') .
            '>';
    }


    protected function textareaField($name, $value, $class, $attributes, $options)
    {
        $readonly = array_pull($options, 'readonly', false);
        $disabled = array_get($options, 'disabled', false);

        return "<textarea
            name='{$name}'
            class='form-control {$class}'
            id='{$name}'
            {$attributes}"
            . ($disabled ? 'disabled' : '')
            . ($readonly ? 'readonly ' : '') .
            ">{$value}</textarea>";
    }


    protected function checkboxField($name, $value, $class, $attributes, $options, $label)
    {
        $checked = array_pull($options, 'checked', false);
        $disabled = array_get($options, 'disabled', false);

        if (!is_null($value)) {
            $checked = $value;
        }

        $html = '<div class="form-check form-switch">';

//        if (!$disabled) {
//            $html .= "<input type='hidden' value='0' name='{$name}'>";
//        value='1'
//        }

        $html .= "<input
                    type='checkbox'
                    name='{$name}'
                    role='switch'
                    class='form-check-input {$class}'
                    id='{$name}'
                    {$attributes}
                    "
            . ($checked ? 'checked ' : '')
            . ($disabled ? 'disabled' : '') .
            '>';

        $html .= "<label class='form-check-label' for='{$name}'>{$label}</label>";
        $html .= '</div>';

        return $html;
    }


    protected function selectField($name, $value, $class, $attributes, $options, $list)
    {
        $multiple = array_get($options, 'multiple', false);
        $disabled = array_get($options, 'disabled', false);
        $readonly = array_pull($options, 'readonly', false);

        $multipleAttrs = '';
        if ($multiple) {
            $multipleAttrs = 'multiple data-choices-removeItem ';
            if (!str_contains($name, '[]')) {
                $name .= '[]';
            }
        }

        $html = "<select
            data-choices
            name='{$name}'
            class='form-control custom-select-black {$class}'
            {$multipleAttrs}
            id='{$name}'
            {$attributes}"
            . ($disabled ? 'disabled' : '')
            . ($readonly ? 'readonly ' : '') .
            '>';



        foreach ($list as $listValue => $listName) {
            $listValue = e($listValue);
            $listName = e($listName);

            if ($multiple && $value instanceof Collection) {
                $selected = $value->where('id', $listValue)->isNotEmpty() ? 'selected' : '';
            } else if ($multiple && is_array($value)) {
                $selected = in_array($listValue, $value) ? 'selected' : '';
            } else {
                $selected = (!is_null($value) && $value == $listValue) ? 'selected' : '';
            }

            $html .= "<option value='{$listValue}' {$selected}>{$listName}</option>";
        }

        $html .= '</select>';

        return $html;
    }


    protected function field($name, $title, $errors, $entity, $options, callable $fieldCallback, ...$args)
    {
        $value = $this->getValue($entity, $name);

        if (is_string($value)) {
            $value = e($value);
        }

        $errorName = $this->normalizeTranslatableFieldNameError($name);

        $name = array_get($options, 'multiple', false) ? "{$name}[]" : $name;
        $required = array_pull($options, 'required', false);
        $help = array_pull($options, 'help', false);

        $class = trim(array_pull($options, 'class') . ' ' . ($errors->has($errorName) ? 'is-invalid' : ''));

        $params = array_merge([
            $name,
            $value,
            $class,
            $this->generateHtmlAttributes($options),
            $options,
        ], $args);

        $labelCol = array_pull($options, 'labelCol', 3);
        $fieldCol = 12 - $labelCol;

        $html = '<div class="form-group mb-3">';

        $html .= $this->label($name, $title, $labelCol, $required);

        $html .= call_user_func_array($fieldCallback, $params);

        if ($help && !$errors->has($errorName)) {
            $html .= "<span class='help-block'>{$help}</span>";
        }

        $html .= $errors->first($errorName, '<div class="invalid-feedback d-block">:message</div>');

        $html .= '</div>';

        return new HtmlString($html);
    }


    protected function generateHtmlAttributes($options = [])
    {
        $this->unsetUnnecessaryAttributes($options);

        $attributes = '';

        foreach ($options as $attr => $value) {
            $attributes .= "{$attr}='{$value}' ";
        }

        return $attributes;
    }


    protected function unsetUnnecessaryAttributes(&$options = [])
    {
        foreach ($this->unnecessaryAttributes as $attribute) {
            if (array_key_exists($attribute, $options)) {
                unset($options[$attribute]);
            }
        }
    }


    protected function label($name, $title, $labelCol = 3, $required = false)
    {
        $html = "<label for='{$name}' class='form-label control-label text-left'>{$title}";

        if ($required) {
            $html .= '<span class="m-l-5 text-red">*</span>';
        }

        return $html .= '</label>';
    }


    protected function parseTranslatableName($name)
    {
        if (preg_match('/(\w+)\[(\w+)\]\[(\w+)\]\[(\w+)\]/', $name, $matches)) {
            return [
                'type' => 'iterable_nested_locale',
                'parent' => $matches[1],
                'index' => $matches[2],
                'locale' => $matches[3],
                'attribute' => $matches[4],
            ];
        }

        if (preg_match('/translatable\[(\w+)\]\[(\w+)\]/', $name, $matches)) {
            return [
                'type' => 'translatable_array',
                'attribute' => $matches[1],
                'locale' => $matches[2],
            ];
        }

        if (preg_match('/(\w+)\[(\w+)\]\[(\w+)\]/', $name, $matches)) {
            return [
                'type' => 'nested_locale',
                'parent' => $matches[1],
                'locale' => $matches[2],
                'attribute' => $matches[3],
            ];
        }


        if (preg_match('/(\w+)\[(\w+)\]/', $name, $matches)) {
            return [
                'type' => 'locale',
                'locale' => $matches[1],
                'attribute' => $matches[2],
            ];
        }

        if (preg_match('/translatable\[(\w+)\]/', $name, $matches)) {
            return [
                'type' => 'translatable',
                'attribute' => $matches[1],
            ];
        }

        return ['type' => 'standard', 'attribute' => $name];
    }

    protected function getValue($entity, $name)
    {
        $parsed = $this->parseTranslatableName($name);
        $normalizedName = $this->normalizeTranslatableFieldName($name);

        $value = null;

        if ($parsed['type'] === 'iterable_nested_locale' && is_object($entity)) {


            $locale = $parsed['locale'];
            $attribute = $parsed['attribute'];

            if (method_exists($entity, 'translate')) {
                $value = optional($entity->translate($locale))->$attribute;
            }

        } else if ($parsed['type'] === 'nested_locale' && is_object($entity)) {

            $parentRelation = $parsed['parent']; // 'meta'
            $locale = $parsed['locale'];
            $attribute = $parsed['attribute'];

            $metaEntity = data_get($entity, $parentRelation);

            if ($metaEntity && method_exists($metaEntity, 'translate')) {
                $value = optional($metaEntity->translate($locale))->$attribute;
            }
        } else if ($parsed['type'] === 'locale' && is_object($entity)) {
            $locale = $parsed['locale'];
            $attribute = $parsed['attribute'];


            if (method_exists($entity, 'translate')) {
                $translatedEntity = $entity->translate($locale);

                if ($translatedEntity && isset($translatedEntity->$attribute)) {
                    $value = $translatedEntity->$attribute;
                }
            }
        } else if ($parsed['type'] === 'translatable_array') {
            $attribute = $parsed['attribute'];
            $locale = $parsed['locale'];

            $translatableData = data_get($entity, $attribute);

            if (is_array($translatableData)) {
                $value = data_get($translatableData, "{$locale}.value");
            }

        } else {

            $attribute = ($parsed['type'] === 'translatable')
                ? $parsed['attribute']
                : $name;

            $camelCaseName = camel_case($attribute);
            if (is_object($entity) && method_exists($entity, $camelCaseName) && $entity->{$camelCaseName}() instanceof Relation) {
                $attribute = $camelCaseName;
            }

            try {
                $value = data_get($entity, $attribute);
            } catch (LogicException $e) {
                $value = $entity->getOriginal($attribute);
            }

            if ($value instanceof Money) {
                $value = $value->amount();
            }
        }

        return old($this->normalizeTranslatableFieldNameError($name), $value);
    }


    private function normalizeTranslatableFieldName($name)
    {
        if (starts_with($name, 'translatable[')) {
            return 'translatable.' . str_between($name, 'translatable[', ']');
        }

        return $name;
    }

    private function normalizeTranslatableFieldNameError($name)
    {
        if (preg_match('/(\w+)\[(\w+)\]\[(\w+)\]\[(\w+)\]/', $name, $matches)) {
            return "{$matches[1]}.{$matches[2]}.{$matches[3]}.{$matches[4]}";
        }

        if (preg_match('/(\w+)\[(\w+)\]\[(\w+)\]/', $name, $matches)) {
            return "{$matches[1]}.{$matches[2]}.{$matches[3]}";
        }

        if (preg_match('/(\w+)\[(\w+)\]/', $name, $matches)) {
            return "{$matches[1]}.{$matches[2]}";
        }

        if (starts_with($name, 'translatable[')) {
            return 'translatable.' . str_between($name, 'translatable[', ']');
        }

        return $name;
    }
}

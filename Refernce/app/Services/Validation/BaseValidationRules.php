<?php

namespace App\Services\Validation;

class BaseValidationRules
{
    /**
     * Get phone validation rules
     */
    public static function phone(bool $required = true): array
    {
        $rules = ['string', 'regex:/^[\+]?[1-9][\d]{0,15}$/'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get email validation rules
     */
    public static function email(bool $required = true): array
    {
        $rules = ['email', 'max:255'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get password validation rules
     */
    public static function password(bool $required = true, int $minLength = 8): array
    {
        $rules = ["min:{$minLength}", 'confirmed'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get name validation rules
     */
    public static function name(bool $required = true, int $maxLength = 255): array
    {
        $rules = ["max:{$maxLength}", 'string'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get URL validation rules
     */
    public static function url(bool $required = true): array
    {
        $rules = ['url'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get file validation rules
     */
    public static function file(bool $required = true, string $mimes = 'jpeg,png,jpg,gif,svg,pdf,doc,docx', int $maxSize = 2048): array
    {
        $rules = ["mimes:{$mimes}", "max:{$maxSize}"];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get image validation rules
     */
    public static function image(bool $required = true, int $maxSize = 2048): array
    {
        return static::file($required, 'jpeg,png,jpg,gif,svg', $maxSize);
    }

    /**
     * Get boolean validation rules
     */
    public static function boolean(bool $required = false): array
    {
        $rules = ['boolean'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get date validation rules
     */
    public static function date(bool $required = true, ?string $after = null, ?string $before = null): array
    {
        $rules = ['date'];

        if ($after) {
            $rules[] = "after:{$after}";
        }

        if ($before) {
            $rules[] = "before:{$before}";
        }

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get numeric validation rules
     */
    public static function numeric(bool $required = true, ?int $min = null, ?int $max = null): array
    {
        $rules = ['numeric'];

        if ($min !== null) {
            $rules[] = "min:{$min}";
        }

        if ($max !== null) {
            $rules[] = "max:{$max}";
        }

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get array validation rules
     */
    public static function array(bool $required = true, ?int $min = null, ?int $max = null): array
    {
        $rules = ['array'];

        if ($min !== null) {
            $rules[] = "min:{$min}";
        }

        if ($max !== null) {
            $rules[] = "max:{$max}";
        }

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Get unique validation rules
     *
     * @param  mixed  $except
     */
    public static function unique(string $table, string $column = 'NULL', $except = null, ?string $connection = null): array
    {
        $rule = "unique:{$table}";

        if ($column !== 'NULL') {
            $rule .= ",{$column}";
        }

        if ($except !== null) {
            $rule .= ",{$except}";
        }

        if ($connection !== null) {
            $rule = "unique:{$connection}.{$table}".($column !== 'NULL' ? ",{$column}" : '');
        }

        return ['unique' => $rule];
    }

    /**
     * Get exists validation rules
     */
    public static function exists(string $table, string $column = 'NULL'): array
    {
        $rule = "exists:{$table}";

        if ($column !== 'NULL') {
            $rule .= ",{$column}";
        }

        return ['exists' => $rule];
    }

    /**
     * Get custom regex validation rules
     */
    public static function regex(string $pattern, bool $required = true): array
    {
        $rules = ["regex:{$pattern}"];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }
}

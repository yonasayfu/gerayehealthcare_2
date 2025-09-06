<?php

namespace App\Services\Validation\Rules;

abstract class BaseResourceRules
{
    abstract public static function store(): array;

    abstract public static function update($model): array;

    protected static function unique(string $table, ?string $column = null, $except = null, string $idColumn = 'id'): string
    {
        $rule = "unique:{$table}";

        if ($column) {
            $rule .= ",{$column}";
        }

        if ($except) {
            $rule .= ",{$except},{$idColumn}";
        }

        return $rule;
    }
}

<?php

namespace App\Services\EligibilityRuleEvaluator;

use App\Models\EligibilityCriteria;

class EligibilityRuleEvaluator
{
    /**
     * Evaluate all eligibility criteria for a given event against a context payload.
     *
     * Context keys should be snake_case versions of domain attributes, e.g.:
     *   - age, city, phone_number, etc.
     * We derive a snake_case key from criteria_title to look up values in the context.
     */
    public function evaluateForEvent(int $eventId, array $context): array
    {
        $criteria = EligibilityCriteria::where('event_id', $eventId)->get();

        $failed = [];
        $checked = 0;
        $missing = [];

        foreach ($criteria as $rule) {
            $key = $this->toSnakeCase($rule->criteria_title);

            if (! array_key_exists($key, $context)) {
                $missing[] = $rule->criteria_title;
                // Skip evaluation when we do not have the necessary data
                continue;
            }

            $candidateValue = $context[$key];
            $operator = strtoupper(trim($rule->operator));
            $ruleValue = $rule->value;
            $checked++;

            if (! $this->compare($candidateValue, $operator, $ruleValue)) {
                $failed[] = [
                    'title' => $rule->criteria_title,
                    'operator' => $rule->operator,
                    'expected' => $rule->value,
                    'actual' => $candidateValue,
                ];
            }
        }

        // If no rules were checked (all missing), we cannot decide – mark unknown.
        if ($checked === 0) {
            return [
                'status' => 'unknown',
                'isEligible' => null,
                'failed' => $failed,
                'missing' => $missing,
                'checked' => $checked,
                'total' => $criteria->count(),
            ];
        }

        $isEligible = empty($failed);

        return [
            'status' => $isEligible ? 'eligible' : 'ineligible',
            'isEligible' => $isEligible,
            'failed' => $failed,
            'missing' => $missing,
            'checked' => $checked,
            'total' => $criteria->count(),
        ];
    }

    protected function toSnakeCase(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9]+/', ' ', $value ?? '');
        $value = trim($value);
        $value = strtolower(preg_replace('/\s+/', '_', $value));

        return $value;
    }

    /**
     * Compare candidate value with rule value using the given operator.
     * Supports: =, ==, !=, <>, >, >=, <, <=, LIKE, ILIKE, IN, NOT IN
     */
    protected function compare($candidate, string $operator, $ruleValue): bool
    {
        // Normalize arrays for IN/NOT IN
        if (in_array($operator, ['IN', 'NOT IN'], true)) {
            $list = array_map('trim', explode(',', (string) $ruleValue));
            $in = in_array((string) $candidate, $list, true);

            return $operator === 'IN' ? $in : ! $in;
        }

        // LIKE/ILIKE using simple %/_ wildcards
        if (in_array($operator, ['LIKE', 'ILIKE'], true)) {
            $pattern = $this->likeToRegex((string) $ruleValue);
            $flags = $operator === 'ILIKE' ? 'i' : '';

            return (bool) preg_match('/'.$pattern.'/'.$flags, (string) $candidate);
        }

        // Coerce to numbers when both are numeric
        $left = $candidate;
        $right = $ruleValue;
        $bothNumeric = is_numeric($left) && is_numeric($right);
        if ($bothNumeric) {
            $left = (float) $left;
            $right = (float) $right;
        } else {
            $left = (string) $left;
            $right = (string) $right;
        }

        return match ($operator) {
            '=', '==' => $left == $right,
            '!=', '<>' => $left != $right,
            '>' => $left > $right,
            '>=' => $left >= $right,
            '<' => $left < $right,
            '<=' => $left <= $right,
            default => false,
        };
    }

    protected function likeToRegex(string $likePattern): string
    {
        // Escape regex special chars, then translate SQL wildcards
        $escaped = preg_quote($likePattern, '/');
        $escaped = str_replace(['%', '_'], ['.*', '.'], $escaped);

        return '^'.$escaped.'$';
    }
}


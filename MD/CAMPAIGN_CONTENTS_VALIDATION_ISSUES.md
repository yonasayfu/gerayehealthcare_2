# Campaign Contents Validation Issues

## Problem Description

When creating a new Campaign Content, users are seeing the generic error message: "Validation failed. Please check your input."

## Root Causes

### 1. Missing Required Fields in Frontend Form

Looking at the validation rules in `CampaignContentRules.php`, the following fields are required:
- `campaign_id`
- `platform_id`
- `content_type`
- `title`
- `scheduled_post_date`
- `status`

However, in the frontend Form.vue component, the `scheduled_post_date` field is not being properly initialized or may be empty when submitted.

### 2. DateTime Format Issues

The frontend uses computed properties to format datetime fields, but there might be issues with how these values are being processed:
- `formattedScheduledPostDate` 
- `formattedActualPostDate`

### 3. JSON Validation for Engagement Metrics

The `engagement_metrics` field expects valid JSON, but the textarea might contain invalid JSON or the form might not be properly handling empty values.

## Recommended Fixes

### 1. Fix DateTime Handling in Form.vue

Update the Form.vue component to ensure proper datetime handling:

1. Make sure the `scheduled_post_date` field has a default value or proper validation
2. Ensure datetime values are properly formatted before submission

### 2. Improve Error Handling

Add better error handling to display specific validation errors instead of the generic message.

### 3. Add Default Values

Ensure all required fields have appropriate default values in the Create.vue form.

## Testing Steps

1. Create a new Campaign Content with all required fields filled
2. Verify that datetime fields are properly formatted
3. Check that engagement_metrics contains valid JSON or is left empty
4. Confirm that error messages are specific and helpful

## Backend Validation Rules

The current validation rules in `CampaignContentRules.php` are:
```php
public static function store(): array
{
    return [
        'campaign_id' => ['required', 'exists:marketing_campaigns,id'],
        'platform_id' => ['required', 'exists:marketing_platforms,id'],
        'content_type' => ['required', 'string', 'max:255'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'media_url' => ['nullable', 'url', 'max:255'],
        'scheduled_post_date' => ['required', 'date'],
        'actual_post_date' => ['nullable', 'date'],
        'status' => ['required', 'string', 'max:255'],
        'engagement_metrics' => ['nullable', 'json'],
    ];
}
```

These rules are strict and will reject submissions that don't meet all requirements.
@php
    $fieldData = $payload['changes']['field_value'] ?? null;
    $fieldType = $fieldData['field_type'] ?? 'text';
    $fieldName = $fieldData['field_name'] ?? 'Unknown Field';

    // Handle from value
    $fromValue = $fieldData['from']['name']
        ?? $fieldData['from']['title']
        ?? $fieldData['from']
        ?? null;

    // Handle to value
    $toValue = $fieldData['to']['name']
        ?? $fieldData['to']['title']
        ?? $fieldData['to']
        ?? null;

    // Special handling for boolean values
    if ($fieldType === 'checkbox') {
        $fromValue = filter_var($fromValue, FILTER_VALIDATE_BOOLEAN);
        $toValue = filter_var($toValue, FILTER_VALIDATE_BOOLEAN);
    }

    // Check if template exists, fallback to unsupported
    $template = "github-project::md.field_types.{$fieldType}";
    if (!view()->exists($template)) {
        $template = 'github-project::md.field_types.unsupported';
    }
@endphp

@include($template, compact('fieldName', 'fromValue', 'toValue', 'fieldData'))

@php
    $fieldData = $payload['changes']['field_value'] ?? null;

    $fieldName = $fieldData['field_name'] ?? 'Unknown Field';
    $fromValue = $fieldData['from']['name'] ?? null;
    $toValue = $fieldData['to']['name'] ?? null;
@endphp
@include(
    'github-project::md.field_types.' . $fieldData['field_type'],
    compact('fieldName', 'fromValue', 'toValue', 'fieldData')
)

@include('github-project::md.shared.author', compact('payload'))

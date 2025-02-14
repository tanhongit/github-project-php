@php
    $fieldName = $payload['projects_v2_item']['field_name'];
    $fromValue = $payload['from']['name'] ?? 'Unknown';
    $toValue = $payload['to']['name'] ?? 'Unknown';
@endphp
@include(
    'github-project::md.field_types.' . $payload['projects_v2_item']['field_type'],
    compact('fieldName', 'fromValue', 'toValue')
)

@include('github-project::md.shared.author', compact('payload'))

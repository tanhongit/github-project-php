**{{ '@'.$editor }}** updated **{{ $fieldName }}** from **{{ $fromValue }}** to **{{ $toValue }}**.

@include('github-project::md.field_types.' . $payload['projects_v2_item']['field_type'], compact('payload'))

@include('github-project::md.shared.author', compact('payload'))

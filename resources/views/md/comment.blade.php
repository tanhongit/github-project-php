@php
    $action = $payload['action'] ?? 'updated';
    $actionVerb = match($action) {
        'created' => 'created',
        'deleted' => 'deleted',
        'edited' => 'updated',
        default => 'updated'
    };
    
    $itemType = $payload['projects_v2_item']['content_type'] ?? 'item';
    $itemUrl = $payload['projects_v2_item']['content_url'] ?? '#';
    $itemTitle = $payload['projects_v2_item']['content_title'] ?? 'item';
    
    $projectUrl = $payload['projects_v2_item']['project_url'] ?? '#';
    $projectTitle = $payload['projects_v2_item']['project_title'] ?? 'project';
@endphp

**📌 {{ ucfirst($actionVerb) }}** in [{{ $projectTitle }}]({{ $projectUrl }})

@include('github-project::md.shared.content', compact('payload'))

---

🔗 [View {{ ucfirst($itemType) }}]({{ $itemUrl }})

@include('github-project::md.shared.author', [
    'name' => $payload['sender']['login'] ?? 'Unknown',
    'html_url' => $payload['sender']['html_url'] ?? '#',
    'avatar_url' => $payload['sender']['avatar_url'] ?? null,
    'date' => $payload['changes']['updated_at'] ?? now()->toIso8601String()
])

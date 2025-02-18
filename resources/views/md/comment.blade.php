@include('github-project::md.shared.content', compact('payload'))

@include('github-project::md.shared.author', [
    'name' => $payload['sender']['login'] ?? 'Unknown',
    'html_url' => $payload['sender']['html_url'] ?? '#'
])

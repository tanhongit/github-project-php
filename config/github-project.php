<?php

$routePrefix = env('GITHUB_PROJECT_ROUTE_PREFIX', 'github-project');

return [
    'name' => 'GitHubProject',

    'route_prefix' => $routePrefix,

    'github' => [
        'access_token' => env('GITHUB_ACCESS_TOKEN', ''),
        'graphql_url' => env('GITHUB_GRAPHQL_URL', 'https://api.github.com/graphql'),
    ],

    'enable_status_comment' => env('GITHUB_PROJECT_ENABLE_STATUS_COMMENT', false),
];

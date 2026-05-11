<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;

$debug = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN);
$isRailway = getenv('RAILWAY_ENVIRONMENT') || getenv('RAILWAY_PROJECT_ID') || getenv('RAILWAY_SERVICE_ID');

return [
    // Toggle the configuration cache. Set this to boolean false, or remove the
    // directive, to disable configuration caching. Toggling development mode
    // will also disable it by default; clear the configuration cache using
    // `composer clear-config-cache`.
    ConfigAggregator::ENABLE_CACHE => ! $debug && ! $isRailway,

    // Enable debugging; typically used to provide debugging information within templates.
    'debug'  => $debug,
    'mezzio' => [
        // Provide templates for the error handling middleware to use when
        // generating responses.
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::error',
        ],
    ],
];

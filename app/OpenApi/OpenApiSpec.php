<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * OpenAPI root (PHP 8 attributes — required by L5-Swagger 11 / swagger-php 6 default scanner).
 * DocBlock @OA annotations are not scanned unless you add DocBlockAnnotationFactory to config.
 */
#[OA\OpenApi(
    openapi: OA\OpenApi::VERSION_3_0_0,
    info: new OA\Info(
        version: '1.0.0',
        title: 'Ravy API',
        description: 'Monolith modular HTTP API'
    ),
    servers: [
        new OA\Server(
            url: '/',
            description: 'Current application host (see also APP_URL / L5_SWAGGER_CONST_HOST)'
        ),
    ],
    paths: [
        new OA\PathItem(
            path: '/up',
            get: new OA\Get(
                operationId: 'healthUp',
                tags: ['System'],
                summary: 'Application health (Laravel)',
                responses: [
                    new OA\Response(response: 200, description: 'OK'),
                ]
            )
        ),
    ],
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'sanctum',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'Sanctum',
                description: 'Laravel Sanctum personal access token: `Bearer {token}`'
            ),
        ]
    ),
)]
final class OpenApiSpec
{
}

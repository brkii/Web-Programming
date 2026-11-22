<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class SystemPaths
{
    #[OA\Get(path: '/', operationId: 'getApi', tags: ['system'], summary: 'API information', responses: [
        new OA\Response(response: 200, description: 'API is running (does not test the database)', content: new OA\JsonContent(type: 'object', required: ['app', 'milestone'], properties: [
            new OA\Property(property: 'app', type: 'string', example: 'PitStop OS API'),
            new OA\Property(property: 'milestone', type: 'integer', example: 3),
        ])),
    ])]
    public function info(): void {}
}

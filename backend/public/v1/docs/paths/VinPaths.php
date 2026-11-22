<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class VinPaths
{
    #[OA\Get(
        path: '/vin/{vin}', operationId: 'decodeVin', tags: ['vin'],
        summary: 'Decode a VIN using NHTSA vPIC',
        description: 'Live external lookup without an API key. 3-second connection and 10-second total timeout. Partial data is returned with complete=false; review values before saving. Upstream failure returns 503 and allows manual vehicle entry.',
        parameters: [
            new OA\Parameter(name: 'vin', in: 'path', required: true, schema: new OA\Schema(type: 'string', pattern: '^[A-HJ-NPR-Z0-9]{17}$'), example: '1HGCM82633A004352'),
            new OA\Parameter(name: 'model_year', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1980, maximum: 2155), example: 2003),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Decoded vehicle, possibly incomplete', content: new OA\JsonContent(
                type: 'object', required: ['vin', 'make', 'model', 'year', 'fuel_type', 'body_class', 'source', 'complete', 'message'], properties: [
                    new OA\Property(property: 'vin', type: 'string'),
                    new OA\Property(property: 'make', type: 'string', nullable: true),
                    new OA\Property(property: 'model', type: 'string', nullable: true),
                    new OA\Property(property: 'year', type: 'integer', nullable: true),
                    new OA\Property(property: 'fuel_type', type: 'string', nullable: true),
                    new OA\Property(property: 'body_class', type: 'string', nullable: true),
                    new OA\Property(property: 'source', type: 'string', example: 'NHTSA vPIC'),
                    new OA\Property(property: 'complete', type: 'boolean'),
                    new OA\Property(property: 'message', type: 'string'),
                ])),
            new OA\Response(response: 400, description: 'Invalid VIN or model year', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'External lookup unavailable; use manual entry', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function decode(): void {}
}

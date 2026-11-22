<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class PartPaths
{
    #[OA\Get(
        path: '/parts',
        operationId: 'listPart',
        tags: ['parts'],
        summary: 'List parts',
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Part'))),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/parts/{id}',
        operationId: 'getPart',
        tags: ['parts'],
        summary: 'Get part',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Part')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function get(): void {}

    #[OA\Post(
        path: '/parts',
        operationId: 'createPart',
        tags: ['parts'],
        summary: 'Create part',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/PartInput')),
        responses: [
            new OA\Response(response: 201, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Part')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/parts/{id}',
        operationId: 'updatePart',
        tags: ['parts'],
        summary: 'Update part',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/PartUpdate')),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Part')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/parts/{id}',
        operationId: 'deletePart',
        tags: ['parts'],
        summary: 'Delete part',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function delete(): void {}

}

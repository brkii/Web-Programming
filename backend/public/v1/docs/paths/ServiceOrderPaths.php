<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class ServiceOrderPaths
{
    #[OA\Get(
        path: '/service-orders',
        operationId: 'listServiceOrder',
        tags: ['service-orders'],
        summary: 'List service orders',
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ServiceOrder'))),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/service-orders/{id}',
        operationId: 'getServiceOrder',
        tags: ['service-orders'],
        summary: 'Get service order',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrder')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function get(): void {}

    #[OA\Post(
        path: '/service-orders',
        operationId: 'createServiceOrder',
        tags: ['service-orders'],
        summary: 'Create service order',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderInput')),
        responses: [
            new OA\Response(response: 201, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrder')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/service-orders/{id}',
        operationId: 'updateServiceOrder',
        tags: ['service-orders'],
        summary: 'Update service order',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderUpdate')),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrder')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/service-orders/{id}',
        operationId: 'deleteServiceOrder',
        tags: ['service-orders'],
        summary: 'Delete service order',
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

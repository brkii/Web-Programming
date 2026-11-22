<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class ServiceOrderItemPaths
{
    #[OA\Get(
        path: '/service-order-items',
        operationId: 'listServiceOrderItem',
        tags: ['service-order-items'],
        summary: 'List service order items',
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ServiceOrderItem'))),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/service-order-items/{id}',
        operationId: 'getServiceOrderItem',
        tags: ['service-order-items'],
        summary: 'Get service order item',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderItem')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function get(): void {}

    #[OA\Post(
        path: '/service-order-items',
        operationId: 'createServiceOrderItem',
        tags: ['service-order-items'],
        summary: 'Create service order item',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderItemInput')),
        responses: [
            new OA\Response(response: 201, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderItem')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/service-order-items/{id}',
        operationId: 'updateServiceOrderItem',
        tags: ['service-order-items'],
        summary: 'Update service order item',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderItemUpdate')),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/ServiceOrderItem')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/service-order-items/{id}',
        operationId: 'deleteServiceOrderItem',
        tags: ['service-order-items'],
        summary: 'Delete service order item',
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

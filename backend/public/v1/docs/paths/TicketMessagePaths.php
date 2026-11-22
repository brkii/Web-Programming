<?php
declare(strict_types=1);
use OpenApi\Attributes as OA;

class TicketMessagePaths
{
    #[OA\Get(
        path: '/ticket-messages',
        operationId: 'listTicketMessage',
        tags: ['ticket-messages'],
        summary: 'List ticket messages',
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TicketMessage'))),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/ticket-messages/{id}',
        operationId: 'getTicketMessage',
        tags: ['ticket-messages'],
        summary: 'Get ticket message',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/TicketMessage')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function get(): void {}

    #[OA\Post(
        path: '/ticket-messages',
        operationId: 'createTicketMessage',
        tags: ['ticket-messages'],
        summary: 'Create ticket message',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TicketMessageInput')),
        responses: [
            new OA\Response(response: 201, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/TicketMessage')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/ticket-messages/{id}',
        operationId: 'updateTicketMessage',
        tags: ['ticket-messages'],
        summary: 'Update ticket message',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 2147483647))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TicketMessageUpdate')),
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/TicketMessage')),
            new OA\Response(response: 400, description: 'Invalid input or business rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 500, description: 'Unexpected server error', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/ticket-messages/{id}',
        operationId: 'deleteTicketMessage',
        tags: ['ticket-messages'],
        summary: 'Delete ticket message',
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

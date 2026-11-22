<?php
declare(strict_types=1);

class HttpException extends RuntimeException
{
    protected int $status = 500;
    public function getStatus(): int { return $this->status; }
}
class ValidationException extends HttpException { protected int $status = 400; }
class UnauthorizedException extends HttpException { protected int $status = 401; }
class ForbiddenException extends HttpException { protected int $status = 403; }
class NotFoundException extends HttpException { protected int $status = 404; }
class ExternalServiceException extends HttpException { protected int $status = 503; }

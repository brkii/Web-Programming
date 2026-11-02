<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class ServiceOrderDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('service_orders', ['service_request_id', 'mechanic_id', 'diagnosis', 'status', 'estimated_cost', 'final_cost']);
    }

    public function getByRequest(int $requestId): ?array
    {
        return $this->queryOne('SELECT * FROM service_orders WHERE service_request_id = :requestId', ['requestId' => $requestId]);
    }

    public function getByMechanic(int $mechanicId): array
    {
        return $this->query('SELECT * FROM service_orders WHERE mechanic_id = :mechanicId ORDER BY id', ['mechanicId' => $mechanicId]);
    }
}

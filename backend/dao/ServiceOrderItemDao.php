<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class ServiceOrderItemDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('service_order_items', ['service_order_id', 'service_id', 'part_id', 'quantity', 'unit_price']);
    }

    public function getByOrder(int $orderId): array
    {
        return $this->query('SELECT i.*, COALESCE(s.name, p.name) AS item_name
            FROM service_order_items i LEFT JOIN services s ON s.id = i.service_id
            LEFT JOIN parts p ON p.id = i.part_id
            WHERE i.service_order_id = :orderId ORDER BY i.id', ['orderId' => $orderId]);
    }

    public function getTotalByOrder(int $orderId): string
    {
        return (string) $this->queryOne('SELECT COALESCE(SUM(quantity * unit_price), 0.00) AS total
            FROM service_order_items WHERE service_order_id = :orderId', ['orderId' => $orderId])['total'];
    }
}

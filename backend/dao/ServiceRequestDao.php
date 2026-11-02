<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class ServiceRequestDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('service_requests', ['vehicle_id', 'requested_by', 'service_id', 'description', 'preferred_date', 'priority', 'status', 'notes']);
    }

    public function getByCustomer(int $customerId): array
    {
        return $this->query('SELECT r.*, v.make, v.model, v.license_plate, s.name AS service_name
            FROM service_requests r JOIN vehicles v ON v.id = r.vehicle_id
            JOIN services s ON s.id = r.service_id
            WHERE r.requested_by = :customerId ORDER BY r.created_at DESC, r.id DESC', ['customerId' => $customerId]);
    }

    public function countByService(): array
    {
        return $this->query('SELECT s.id, s.name, COUNT(r.id) AS total FROM services s
            LEFT JOIN service_requests r ON r.service_id = s.id
            GROUP BY s.id, s.name ORDER BY s.id');
    }
}

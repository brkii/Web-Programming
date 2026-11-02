<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class SupportTicketDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('support_tickets', ['customer_id', 'service_request_id', 'subject', 'description', 'category', 'priority', 'status']);
    }

    public function getByCustomer(int $customerId): array
    {
        return $this->query('SELECT * FROM support_tickets WHERE customer_id = :customerId ORDER BY created_at DESC, id DESC', ['customerId' => $customerId]);
    }
}

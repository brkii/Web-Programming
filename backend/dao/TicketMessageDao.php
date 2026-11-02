<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class TicketMessageDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('ticket_messages', ['ticket_id', 'sender_id', 'message']);
    }

    public function getByTicket(int $ticketId): array
    {
        return $this->query('SELECT m.*, u.full_name AS sender_name FROM ticket_messages m
            JOIN users u ON u.id = m.sender_id WHERE m.ticket_id = :ticketId
            ORDER BY m.created_at, m.id', ['ticketId' => $ticketId]);
    }
}

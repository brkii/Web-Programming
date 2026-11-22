<?php
declare(strict_types=1);

class TicketMessageService extends BaseService
{
    protected string $entityName = 'Ticket message';
    protected array $fields = ['ticket_id', 'sender_id', 'message'];
    public function __construct(?TicketMessageDao $dao = null) { parent::__construct($dao ?? new TicketMessageDao()); }

    private function openTicket(mixed $id): array
    {
        $ticket = $this->related(new SupportTicketDao(), $id, 'ticket_id');
        if (in_array($ticket['status'], ['Resolved', 'Closed'], true)) { throw new ValidationException('Reopen a resolved ticket before changing its conversation. Closed tickets are read-only.'); }
        return $ticket;
    }

    protected function validate(array $data, ?array $old): array
    {
        $ticket = $this->openTicket($data['ticket_id'] ?? null);
        $sender = $this->related(new UserDao(), $data['sender_id'] ?? null, 'sender_id');
        if ($sender['role'] === 'customer' && $sender['id'] !== $ticket['customer_id']) { throw new ValidationException('A customer can only write in their own ticket.'); }
        $data['ticket_id'] = $ticket['id'];
        $data['sender_id'] = $sender['id'];
        $data['message'] = Validator::text($data['message'] ?? null, 'message', 10000);
        if ($old) {
            $this->immutable($data, $old, ['ticket_id', 'sender_id']);
            $this->editableMessage($old);
        }
        return $data;
    }

    private function editableMessage(array $row): void
    {
        $messages = $this->dao->getByTicket($row['ticket_id']);
        if ($messages[0]['id'] === $row['id']) { throw new ValidationException('The opening message is part of the ticket record and cannot be edited or deleted.'); }
        if (end($messages)['id'] !== $row['id']) { throw new ValidationException('Only the latest reply can be edited or deleted.'); }
    }

    protected function beforeDelete(array $row): void
    {
        $this->openTicket($row['ticket_id']);
        $this->editableMessage($row);
    }
}

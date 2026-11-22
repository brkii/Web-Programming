<?php
declare(strict_types=1);

class SupportTicketService extends BaseService
{
    protected string $entityName = 'Support ticket';
    protected array $fields = ['customer_id', 'service_request_id', 'subject', 'description', 'category', 'priority', 'status'];
    protected array $defaults = ['service_request_id' => null, 'priority' => 'Normal', 'status' => 'Open'];
    public function __construct(?SupportTicketDao $dao = null) { parent::__construct($dao ?? new SupportTicketDao()); }

    public function create(array $data): array
    {
        return $this->atomic(function () use ($data) {
            $ticket = parent::create($data);
            (new TicketMessageService())->create(['ticket_id' => $ticket['id'], 'sender_id' => $ticket['customer_id'], 'message' => $ticket['description']]);
            return $ticket;
        });
    }

    protected function validate(array $data, ?array $old): array
    {
        $data['customer_id'] = $this->related(new UserDao(), $data['customer_id'] ?? null, 'customer_id', 'customer')['id'];
        if ($data['service_request_id'] !== null) {
            $request = $this->related(new ServiceRequestDao(), $data['service_request_id'], 'service_request_id');
            if ($request['requested_by'] !== $data['customer_id']) { throw new ValidationException('The linked request must belong to this customer.'); }
            $data['service_request_id'] = $request['id'];
        }
        $data['subject'] = Validator::text($data['subject'] ?? null, 'subject', 200);
        $data['description'] = Validator::text($data['description'] ?? null, 'description', 10000);
        $data['category'] = Validator::text($data['category'] ?? null, 'category');
        $data['priority'] = Validator::choice($data['priority'], 'priority', ['Normal', 'High', 'Urgent']);
        if (!$old) { Validator::choice($data['status'], 'status', ['Open']); }
        else {
            $this->immutable($data, $old, ['customer_id', 'description']);
            $transitions = [
                'Open' => ['Open', 'In Progress', 'Resolved', 'Closed'],
                'In Progress' => ['In Progress', 'Waiting for Customer', 'Resolved', 'Closed'],
                'Waiting for Customer' => ['Waiting for Customer', 'In Progress', 'Resolved', 'Closed'],
                'Resolved' => ['Resolved', 'In Progress', 'Closed'], 'Closed' => ['Closed'],
            ];
            Validator::choice($data['status'], 'status', $transitions[$old['status']]);
            if ($old['status'] === 'Closed') { $this->immutable($data, $old, $this->fields); }
        }
        return $data;
    }

    public function delete(mixed $id): void
    {
        $id = Validator::integer($id, 'id');
        $this->atomic(function () use ($id) {
            $ticket = $this->dao->lockById($id) ?? throw new NotFoundException('Support ticket not found.');
            $messages = new TicketMessageDao();
            $rows = $messages->getByTicket($id);
            if ($ticket['status'] !== 'Open' || count($rows) !== 1 || $rows[0]['sender_id'] !== $ticket['customer_id']) {
                throw new ValidationException('Only an unanswered open ticket can be deleted. Close conversations to preserve their history.');
            }
            $messages->delete($rows[0]['id']);
            $this->dao->delete($id);
        });
    }
}

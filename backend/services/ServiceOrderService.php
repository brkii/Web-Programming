<?php
declare(strict_types=1);

class ServiceOrderService extends BaseService
{
    protected string $entityName = 'Service order';
    protected array $fields = ['service_request_id', 'mechanic_id', 'diagnosis', 'status', 'estimated_cost'];
    protected array $defaults = ['mechanic_id' => null, 'diagnosis' => null, 'status' => 'Approved', 'estimated_cost' => '0.00'];
    public function __construct(?ServiceOrderDao $dao = null) { parent::__construct($dao ?? new ServiceOrderDao()); }

    public function create(array $data): array
    {
        return $this->atomic(function () use ($data) {
            $order = parent::create($data);
            $request = (new ServiceRequestDao())->getById($order['service_request_id']);
            (new ServiceOrderItemService())->create(['service_order_id' => $order['id'], 'service_id' => $request['service_id'], 'quantity' => 1]);
            return $order;
        });
    }

    protected function validate(array $data, ?array $old): array
    {
        $requests = new ServiceRequestDao();
        $request = $this->related($requests, $data['service_request_id'] ?? null, 'service_request_id');
        $data['service_request_id'] = $request['id'];
        if ($data['mechanic_id'] !== null) { $data['mechanic_id'] = $this->related(new UserDao(), $data['mechanic_id'], 'mechanic_id', 'mechanic')['id']; }
        $data['diagnosis'] = Validator::text($data['diagnosis'], 'diagnosis', 10000, true);
        $data['estimated_cost'] = Validator::money($data['estimated_cost'], 'estimated_cost');
        if (!$old) {
            Validator::choice($data['status'], 'status', ['Approved']);
            if (!in_array($request['status'], ['Requested', 'Approved'], true)) { throw new ValidationException('Only a pending or approved request can become an order.'); }
            $this->unique('service_request_id', $request['id'], null);
            $data['final_cost'] = null;
        } else {
            $this->immutable($data, $old, ['service_request_id']);
            $transitions = [
                'Approved' => ['Approved', 'Diagnosing', 'In Progress', 'Cancelled'],
                'Diagnosing' => ['Diagnosing', 'In Progress', 'Cancelled'],
                'In Progress' => ['In Progress', 'Ready for Pickup', 'Cancelled'],
                'Ready for Pickup' => ['Ready for Pickup', 'In Progress', 'Completed'],
                'Completed' => ['Completed'], 'Cancelled' => ['Cancelled'],
            ];
            Validator::choice($data['status'], 'status', $transitions[$old['status']]);
            if (in_array($old['status'], ['Completed', 'Cancelled'], true)) { $this->immutable($data, $old, $this->fields); }
            if (in_array($data['status'], ['Diagnosing', 'In Progress', 'Ready for Pickup', 'Completed'], true) && $data['mechanic_id'] === null) {
                throw new ValidationException('Assign a mechanic before starting work.');
            }
            $items = new ServiceOrderItemDao();
            if ($data['status'] === 'Completed') {
                if (!$items->getByOrder($old['id'])) { throw new ValidationException('An order must contain at least one item.'); }
                $data['final_cost'] = Validator::money($items->getTotalByOrder($old['id']), 'final_cost');
            } else { $data['final_cost'] = null; }
            if ($data['status'] === 'Cancelled' && $old['status'] !== 'Cancelled') {
                $parts = new PartService();
                foreach ($items->getByOrder($old['id']) as $item) {
                    if ($item['part_id'] !== null) { $parts->changeStock($item['part_id'], $item['quantity']); }
                }
            }
        }
        $requests->update($request['id'], ['status' => $data['status']]);
        return $data;
    }

    public function delete(mixed $id): void
    {
        $id = Validator::integer($id, 'id');
        $this->atomic(function () use ($id) {
            $order = $this->dao->lockById($id) ?? throw new NotFoundException('Service order not found.');
            if ($order['status'] !== 'Approved') { throw new ValidationException('Only an order that has not started can be deleted. Cancel active orders and preserve their history.'); }
            $requests = new ServiceRequestDao();
            $requests->lockById($order['service_request_id']);
            $items = new ServiceOrderItemDao();
            $parts = new PartService();
            foreach ($items->getByOrder($id) as $item) {
                if ($item['part_id'] !== null) { $parts->changeStock($item['part_id'], $item['quantity']); }
                $items->delete($item['id']);
            }
            $this->dao->delete($id);
            $requests->update($order['service_request_id'], ['status' => 'Requested']);
        });
    }
}

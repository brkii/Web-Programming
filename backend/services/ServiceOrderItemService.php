<?php
declare(strict_types=1);

class ServiceOrderItemService extends BaseService
{
    protected string $entityName = 'Order item';
    protected array $fields = ['service_order_id', 'service_id', 'part_id', 'quantity'];
    protected array $defaults = ['service_id' => null, 'part_id' => null];
    public function __construct(?ServiceOrderItemDao $dao = null) { parent::__construct($dao ?? new ServiceOrderItemDao()); }

    private function editableOrder(mixed $id): array
    {
        $order = $this->related(new ServiceOrderDao(), $id, 'service_order_id');
        if (!in_array($order['status'], ['Approved', 'Diagnosing', 'In Progress'], true)) { throw new ValidationException('Items are locked when an order is ready, completed or cancelled.'); }
        return $order;
    }

    protected function validate(array $data, ?array $old): array
    {
        $data['service_order_id'] = $this->editableOrder($data['service_order_id'] ?? null)['id'];
        if (($data['service_id'] === null) === ($data['part_id'] === null)) { throw new ValidationException('Select exactly one service_id or part_id.'); }
        $data['quantity'] = Validator::integer($data['quantity'] ?? null, 'quantity', 1, 1000000);
        if ($old) { $this->immutable($data, $old, ['service_order_id', 'service_id', 'part_id']); }
        $parts = new PartDao();
        if ($data['part_id'] !== null) {
            $part = $this->related($parts, $data['part_id'], 'part_id');
            $data['part_id'] = $part['id'];
            $data['unit_price'] = $old['unit_price'] ?? $part['unit_price'];
            $delta = ($old['quantity'] ?? 0) - $data['quantity'];
            (new PartService($parts))->changeStock($part['id'], $delta);
        } else {
            $service = $this->related(new ServiceDao(), $data['service_id'], 'service_id');
            $data['service_id'] = $service['id'];
            $data['unit_price'] = $old['unit_price'] ?? $service['standard_price'];
        }
        $cents = static fn(string $amount): int => (int) str_replace('.', '', Validator::money($amount, 'price'));
        $total = $cents($this->dao->getTotalByOrder($data['service_order_id']));
        $total += $data['quantity'] * $cents($data['unit_price']) - ($old ? $old['quantity'] * $cents($old['unit_price']) : 0);
        if ($total > 9999999999) { throw new ValidationException('Order total exceeds the supported amount.'); }
        return $data;
    }

    protected function beforeDelete(array $row): void
    {
        $this->editableOrder($row['service_order_id']);
        if (count($this->dao->getByOrder($row['service_order_id'])) <= 1) { throw new ValidationException('An order must keep at least one item.'); }
        if ($row['part_id'] !== null) { (new PartService())->changeStock($row['part_id'], $row['quantity']); }
    }
}

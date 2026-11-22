<?php
declare(strict_types=1);

class ServiceRequestService extends BaseService
{
    protected string $entityName = 'Service request';
    protected array $fields = ['vehicle_id', 'requested_by', 'service_id', 'description', 'preferred_date', 'priority', 'status', 'notes'];
    protected array $defaults = ['priority' => 'Normal', 'status' => 'Requested', 'notes' => null];
    public function __construct(?ServiceRequestDao $dao = null) { parent::__construct($dao ?? new ServiceRequestDao()); }

    protected function validate(array $data, ?array $old): array
    {
        $vehicle = $this->related(new VehicleDao(), $data['vehicle_id'] ?? null, 'vehicle_id');
        $customer = $this->related(new UserDao(), $data['requested_by'] ?? null, 'requested_by', 'customer');
        if ($vehicle['user_id'] !== $customer['id']) { throw new ValidationException('The requester must own the selected vehicle.'); }
        $data['vehicle_id'] = $vehicle['id'];
        $data['requested_by'] = $customer['id'];
        $data['service_id'] = $this->related(new ServiceDao(), $data['service_id'] ?? null, 'service_id')['id'];
        $data['description'] = Validator::text($data['description'] ?? null, 'description', 10000);
        $data['notes'] = Validator::text($data['notes'], 'notes', 10000, true);
        $data['preferred_date'] = Validator::date($data['preferred_date'] ?? null, 'preferred_date');
        $data['priority'] = Validator::choice($data['priority'], 'priority', ['Normal', 'High', 'Urgent']);
        if (!$old) {
            Validator::choice($data['status'], 'status', ['Requested']);
        } else {
            $this->immutable($data, $old, ['vehicle_id', 'requested_by']);
            $order = (new ServiceOrderDao())->getByRequest($old['id']);
            if ($order) {
                $this->immutable($data, $old, ['status', 'service_id']);
            } else {
                $allowed = ['Requested' => ['Requested', 'Approved', 'Cancelled'], 'Approved' => ['Approved', 'Cancelled'], 'Cancelled' => ['Cancelled']];
                Validator::choice($data['status'], 'status', $allowed[$old['status']] ?? [$old['status']]);
            }
            if (in_array($old['status'], ['Completed', 'Cancelled'], true)) { $this->immutable($data, $old, $this->fields); }
        }
        return $data;
    }

    protected function beforeDelete(array $row): void
    {
        if (!in_array($row['status'], ['Requested', 'Cancelled'], true)) { throw new ValidationException('Only unprocessed or cancelled requests without linked records can be deleted.'); }
    }
}

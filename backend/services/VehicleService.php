<?php
declare(strict_types=1);

class VehicleService extends BaseService
{
    protected string $entityName = 'Vehicle';
    protected array $fields = ['user_id', 'vin', 'make', 'model', 'year', 'license_plate', 'mileage', 'fuel_type', 'transmission'];
    protected array $defaults = ['vin' => null, 'mileage' => 0];
    public function __construct(?VehicleDao $dao = null) { parent::__construct($dao ?? new VehicleDao()); }

    protected function validate(array $data, ?array $old): array
    {
        $data['user_id'] = $this->related(new UserDao(), $data['user_id'] ?? null, 'user_id', 'customer')['id'];
        foreach (['make', 'model'] as $field) { $data[$field] = Validator::text($data[$field] ?? null, $field); }
        $data['license_plate'] = strtoupper(Validator::text($data['license_plate'] ?? null, 'license_plate', 30));
        $data['vin'] = ($data['vin'] === null || (is_string($data['vin']) && trim($data['vin']) === '')) ? null : Validator::vin($data['vin']);
        $data['year'] = Validator::integer($data['year'] ?? null, 'year', 1886, 2155);
        $data['mileage'] = Validator::integer($data['mileage'], 'mileage', 0);
        $data['fuel_type'] = Validator::choice($data['fuel_type'] ?? null, 'fuel_type', ['Petrol', 'Diesel', 'Hybrid', 'Electric']);
        $data['transmission'] = Validator::choice($data['transmission'] ?? null, 'transmission', ['Automatic', 'Manual']);
        $this->unique('license_plate', $data['license_plate'], $old);
        $this->unique('vin', $data['vin'], $old);
        if ($old && (new ServiceRequestDao())->findBy('vehicle_id', $old['id'])) { $this->immutable($data, $old, ['user_id']); }
        return $data;
    }
}

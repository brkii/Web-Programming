<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class VehicleDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('vehicles', ['user_id', 'vin', 'make', 'model', 'year', 'license_plate', 'mileage', 'fuel_type', 'transmission']);
    }

    protected function onlyColumns(array $data): array
    {
        $data = parent::onlyColumns($data);
        if (isset($data['vin']) && trim($data['vin']) === '') {
            $data['vin'] = null;
        }
        return $data;
    }

    public function getByOwner(int $userId): array
    {
        return $this->query('SELECT * FROM vehicles WHERE user_id = :userId ORDER BY id', ['userId' => $userId]);
    }
}

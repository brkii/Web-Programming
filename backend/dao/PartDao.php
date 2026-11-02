<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class PartDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('parts', ['name', 'sku', 'category', 'quantity', 'unit_price', 'minimum_stock']);
    }

    public function getLowStock(): array
    {
        return $this->query('SELECT * FROM parts WHERE quantity <= minimum_stock ORDER BY quantity, id');
    }

    public function changeStock(int $id, int $delta): int
    {
        return $this->execute('UPDATE parts SET quantity = quantity + :delta
            WHERE id = :id AND quantity + :checkDelta >= 0',
            ['id' => $id, 'delta' => $delta, 'checkDelta' => $delta]);
    }
}

<?php
declare(strict_types=1);

class PartService extends BaseService
{
    protected string $entityName = 'Part';
    protected array $fields = ['name', 'sku', 'category', 'quantity', 'unit_price', 'minimum_stock'];
    protected array $defaults = ['quantity' => 0, 'minimum_stock' => 0];
    public function __construct(?PartDao $dao = null) { parent::__construct($dao ?? new PartDao()); }

    public function changeStock(int $id, int $delta): void
    {
        if ($delta === 0) { return; }
        if ($this->dao->changeStock($id, $delta) !== 1) {
            throw new ValidationException('Stock change exceeds the available quantity or the maximum stock limit.');
        }
    }

    protected function validate(array $data, ?array $old): array
    {
        foreach (['name', 'sku', 'category'] as $field) { $data[$field] = Validator::text($data[$field] ?? null, $field); }
        $data['sku'] = strtoupper($data['sku']);
        $data['quantity'] = Validator::integer($data['quantity'], 'quantity', 0);
        $data['minimum_stock'] = Validator::integer($data['minimum_stock'], 'minimum_stock', 0);
        $data['unit_price'] = Validator::money($data['unit_price'] ?? null, 'unit_price');
        $this->unique('sku', $data['sku'], $old);
        return $data;
    }
}

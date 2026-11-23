<?php
declare(strict_types=1);

class ServiceService extends BaseService
{
    protected string $entityName = 'Service';
    protected array $fields = ['name', 'description', 'standard_price', 'estimated_duration'];
    public function __construct(?ServiceDao $dao = null) { parent::__construct($dao ?? new ServiceDao()); }

    protected function validate(array $data, ?array $old): array
    {
        $data['name'] = Validator::text($data['name'] ?? null, 'name');
        $data['description'] = Validator::text($data['description'] ?? null, 'description', 10000);
        $data['standard_price'] = Validator::money($data['standard_price'] ?? null, 'standard_price');
        $data['estimated_duration'] = Validator::integer($data['estimated_duration'] ?? null, 'estimated_duration');
        return $data;
    }
}

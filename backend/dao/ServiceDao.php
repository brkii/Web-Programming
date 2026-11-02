<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class ServiceDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('services', ['name', 'description', 'standard_price', 'estimated_duration']);
    }
}

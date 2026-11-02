<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    protected string $readColumns = 'id, full_name, email, role, city, created_at';

    public function __construct()
    {
        parent::__construct('users', ['full_name', 'email', 'password_hash', 'role', 'city']);
    }

    public function getByEmail(string $email): ?array
    {
        return $this->queryOne("SELECT {$this->readColumns} FROM users WHERE email = :email", ['email' => $email]);
    }

    // For the future authentication service only; never send this row to the UI.
    public function getCredentialsByEmail(string $email): ?array
    {
        return $this->queryOne('SELECT id, email, password_hash, role FROM users WHERE email = :email', ['email' => $email]);
    }
}

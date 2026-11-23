<?php
declare(strict_types=1);

class UserService extends BaseService
{
    protected string $entityName = 'User';
    protected array $fields = ['full_name', 'email', 'password', 'role', 'city'];
    protected array $defaults = ['role' => 'customer'];
    public function __construct(?UserDao $dao = null) { parent::__construct($dao ?? new UserDao()); }

    protected function validate(array $data, ?array $old): array
    {
        $data['full_name'] = Validator::text($data['full_name'] ?? null, 'full_name');
        $data['city'] = Validator::text($data['city'] ?? null, 'city');
        $data['email'] = strtolower(Validator::text($data['email'] ?? null, 'email', 190));
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { throw new ValidationException('Enter a valid email address.'); }
        $this->unique('email', $data['email'], $old);
        $data['role'] = Validator::choice($data['role'], 'role', ['customer', 'admin', 'mechanic']);
        if ($old && $data['role'] !== $old['role']) {
            foreach ([[new VehicleDao(), 'user_id'], [new ServiceRequestDao(), 'requested_by'], [new ServiceOrderDao(), 'mechanic_id'], [new SupportTicketDao(), 'customer_id'], [new TicketMessageDao(), 'sender_id']] as [$dao, $field]) {
                if ($dao->findBy($field, $old['id'])) { throw new ValidationException('A user with existing activity cannot change role.'); }
            }
        }
        if (!$old || array_key_exists('password', $data)) {
            $password = $data['password'] ?? null;
            if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0") || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                throw new ValidationException('Password must be 8–72 bytes and contain a letter and a number.');
            }
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }
        unset($data['password']);
        return $data;
    }
}

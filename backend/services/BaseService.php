<?php
declare(strict_types=1);

abstract class BaseService
{
    protected string $entityName = 'Record';
    protected array $fields = [];
    protected array $defaults = [];

    public function __construct(protected BaseDao $dao) {}
    public function getAll(): array { return $this->dao->getAll(); }

    public function getById(mixed $id): array
    {
        return $this->dao->getById(Validator::integer($id, 'id')) ?? throw new NotFoundException("{$this->entityName} not found.");
    }

    public function create(array $data): array
    {
        Validator::fields($data, $this->fields);
        return $this->atomic(function () use ($data) {
            $values = $this->validate(array_replace($this->defaults, $data), null);
            return $this->dao->insert($values);
        });
    }

    // PUT accepts a partial object; omitted fields retain their current values.
    public function update(mixed $id, array $data): array
    {
        $id = Validator::integer($id, 'id');
        Validator::fields($data, $this->fields);
        if ($data === []) { throw new ValidationException('Provide at least one writable field.'); }
        return $this->atomic(function () use ($id, $data) {
            $old = $this->dao->lockById($id) ?? throw new NotFoundException("{$this->entityName} not found.");
            $values = $this->validate(array_replace(array_intersect_key($old, array_flip($this->fields)), $data), $old);
            $this->dao->update($id, $values);
            return $this->getById($id);
        });
    }

    public function delete(mixed $id): void
    {
        $id = Validator::integer($id, 'id');
        $this->atomic(function () use ($id) {
            $row = $this->dao->lockById($id) ?? throw new NotFoundException("{$this->entityName} not found.");
            $this->beforeDelete($row);
            $this->dao->delete($id);
        });
    }

    abstract protected function validate(array $data, ?array $old): array;
    protected function beforeDelete(array $row): void {}

    protected function related(BaseDao $dao, mixed $id, string $field, ?string $role = null): array
    {
        $row = $dao->lockById(Validator::integer($id, $field));
        if (!$row || ($role !== null && $row['role'] !== $role)) {
            throw new ValidationException("$field must refer to an existing " . ($role ?? 'record') . '.');
        }
        return $row;
    }

    protected function unique(string $field, mixed $value, ?array $old): void
    {
        if ($value === null) { return; }
        foreach ($this->dao->findBy($field, $value) as $row) {
            if ($row['id'] !== ($old['id'] ?? null)) { throw new ValidationException("$field is already in use."); }
        }
    }

    protected function immutable(array $data, array $old, array $fields): void
    {
        foreach ($fields as $field) {
            if (($data[$field] ?? null) != ($old[$field] ?? null)) { throw new ValidationException("$field cannot be changed on this record."); }
        }
    }

    protected function atomic(callable $operation): mixed
    {
        $this->dao->beginTransaction();
        try {
            $result = $operation();
            $this->dao->commit();
            return $result;
        } catch (Throwable $e) {
            $this->dao->rollback();
            if ($e instanceof PDOException && in_array((int) ($e->errorInfo[1] ?? 0), [1062, 1451, 1452, 3819, 4025], true)) {
                throw new ValidationException('The change conflicts with an existing record or a database constraint. Referenced history cannot be deleted.', 0, $e);
            }
            throw $e;
        }
    }
}

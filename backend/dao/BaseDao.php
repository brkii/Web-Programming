<?php
declare(strict_types=1);

abstract class BaseDao
{
    private static ?PDO $connection = null;
    protected string $table;
    protected array $columns;
    protected string $readColumns = '*';

    // Identifiers come only from child classes, never from request input.
    protected function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $configPath = __DIR__ . '/../config/config.php';
            if (!is_file($configPath)) {
                throw new RuntimeException('Copy backend/config/config.example.php to config.php and configure MySQL first.');
            }
            require_once $configPath;
            $dsn = 'mysql:host=' . Config::DB_HOST() . ';port=' . Config::DB_PORT()
                . ';dbname=' . Config::DB_NAME() . ';charset=utf8mb4';
            self::$connection = new PDO($dsn, Config::DB_USER(), Config::DB_PASSWORD(), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }

    protected function query(string $sql, array $params = []): array
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    protected function queryOne(string $sql, array $params = []): ?array
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    protected function execute(string $sql, array $params = []): int
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }

    public function getAll(): array
    {
        return $this->query("SELECT {$this->readColumns} FROM `{$this->table}` ORDER BY id");
    }

    public function getById(int $id): ?array
    {
        return $this->queryOne("SELECT {$this->readColumns} FROM `{$this->table}` WHERE id = :id", ['id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->queryOne("SELECT COUNT(*) AS total FROM `{$this->table}`")['total'];
    }

    public function insert(array $data): array
    {
        $data = $this->onlyColumns($data);
        if ($data === []) {
            throw new InvalidArgumentException('Insert requires at least one writable column.');
        }
        $columns = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->execute("INSERT INTO `{$this->table}` ($columns) VALUES ($placeholders)", $data);
        // Read back database defaults; UserDao's projection also excludes password_hash.
        return $this->getById((int) self::connection()->lastInsertId());
    }

    // Returns changed rows, so 0 can mean an unchanged value OR a missing row.
    public function update(int $id, array $data): int
    {
        $data = $this->onlyColumns($data);
        if ($data === []) {
            return 0;
        }
        $fields = [];
        foreach (array_keys($data) as $column) {
            $fields[] = "`$column` = :$column";
        }
        $data['rowId'] = $id;
        return $this->execute("UPDATE `{$this->table}` SET " . implode(', ', $fields)
            . ' WHERE id = :rowId', $data);
    }

    public function delete(int $id): int
    {
        return $this->execute("DELETE FROM `{$this->table}` WHERE id = :id", ['id' => $id]);
    }

    protected function onlyColumns(array $data): array
    {
        return array_intersect_key($data, array_flip($this->columns));
    }

    // Services will decide which operations belong in a transaction in M3.
    public function beginTransaction(): void { self::connection()->beginTransaction(); }
    public function commit(): void { self::connection()->commit(); }
    public function rollback(): void
    {
        if (self::connection()->inTransaction()) {
            self::connection()->rollBack();
        }
    }
}

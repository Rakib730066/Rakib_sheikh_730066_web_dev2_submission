<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;
use PDOStatement;

class BaseRepository
{
    protected PDO $connection;
    protected string $table;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    protected function select(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function selectObjects(string $sql, string $className, array $params = []): array
    {
        $statement = $this->execute($sql, $params);
        return $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className);
    }

    protected function selectOne(string $sql, array $params = []): ?array
    {
        $result = $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    protected function selectOneObject(string $sql, string $className, array $params = []): ?object
    {
        $statement = $this->execute($sql, $params);
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className);
        $result = $statement->fetch();
        return $result ?: null;
    }

    protected function insert(string $sql, array $params = []): int
    {
        $this->execute($sql, $params);
        return (int) $this->connection->lastInsertId();
    }

    protected function update(string $sql, array $params = []): int
    {
        return $this->execute($sql, $params)->rowCount();
    }

    protected function delete(string $sql, array $params = []): int
    {
        return $this->execute($sql, $params)->rowCount();
    }

    protected function execute(string $sql, array $params = []): PDOStatement
    {
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    protected function commit(): void
    {
        $this->connection->commit();
    }

    protected function rollback(): void
    {
        $this->connection->rollBack();
    }
}

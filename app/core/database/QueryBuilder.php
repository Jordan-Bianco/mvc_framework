<?php

namespace App\core\database;

use PDOStatement;

class QueryBuilder
{
    private \PDO $pdo;
    private ?string $query;
    private ?PDOStatement $statement;
    private array $params = [];

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array $fields
     * @return QueryBuilder
     */
    public function select(array $fields = []): QueryBuilder
    {
        if (empty($fields)) {
            $fields = '*';
        } else {
            $fields = implode(', ', $fields);
        }

        $this->query = "SELECT $fields";

        return $this;
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    public function from(string $table): QueryBuilder
    {
        $this->query .= " FROM $table";

        return $this;
    }

    /**
     * @param string $expression
     * @return QueryBuilder
     */
    public function count(string $expression = '*'): QueryBuilder
    {
        $this->query = "SELECT COUNT($expression)";

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $condition
     * @param string|null $tablePrefix
     * @return QueryBuilder
     */
    public function where(string $field, string $value, string $condition = '=', ?string $tablePrefix = null): QueryBuilder
    {
        $this->query .= " WHERE $tablePrefix$field $condition :$field";

        $this->statement = $this->pdo->prepare($this->query);

        $this->params[] = [":$field" => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $condition
     * @param string|null $tablePrefix
     * @return QueryBuilder
     */
    public function andWhere(string $field, string $value, ?string $condition = '=', ?string $tablePrefix = null): QueryBuilder
    {
        $this->query .= " AND $tablePrefix$field $condition :$field";

        $this->statement = $this->pdo->prepare($this->query);

        $this->params[] = [":$field" => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $subQuery
     * @param string $value
     * @param string $condition
     * @return QueryBuilder
     */
    public function whereSubquery(string $field, string $subQuery, string $value, ?string $condition = '='): QueryBuilder
    {
        $subQueryField = trim(explode(':', $subQuery)[1], ')');

        $statement = $this->pdo->prepare($subQuery);
        $statement->bindParam(":$subQueryField", $value);
        $statement->execute();
        $subQueryResult = $statement->fetchColumn();

        $this->query .= " WHERE $field $condition $subQueryResult";

        $this->statement = $this->pdo->prepare($this->query);

        return $this;
    }

    /**
     * @param string $field
     * @param array $array
     * @param string|null $tablePrefix
     * @return QueryBuilder
     */
    public function whereIn(string $field, array $array, ?string $tablePrefix = null): QueryBuilder
    {
        $array = implode(', ', $array);

        $this->query .= " WHERE $tablePrefix$field IN ($array)";

        $this->statement = $this->pdo->prepare($this->query);

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $condition
     * @param string|null $tablePrefix
     * @return QueryBuilder
     */
    public function having(string $field, string $value, ?string $condition = '=', ?string $tablePrefix = null): QueryBuilder
    {
        $this->query .= " HAVING $tablePrefix$field $condition :$field";

        $this->statement = $this->pdo->prepare($this->query);

        $this->params[] = [":$field" => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param array $array
     * @param string|null $tablePrefix
     * @return QueryBuilder
     */
    public function havingIn(string $field, array $array, ?string $tablePrefix = null): QueryBuilder
    {
        $array = implode(', ', $array);

        $this->query .= " HAVING $tablePrefix$field IN ($array)";

        $this->statement = $this->pdo->prepare($this->query);

        return $this;
    }

    /**
     * @param string $id
     * @return QueryBuilder
     */
    public function groupBy(string $id): QueryBuilder
    {
        $this->query .= " GROUP BY $id";

        return $this;
    }

    /**
     * @param int $offset
     * @param int $rowCount
     * @return QueryBuilder
     */
    public function limit(int $offset, int $rowCount): QueryBuilder
    {
        $this->query .= " LIMIT $offset, $rowCount";

        return $this;
    }

    /**
     * @param string $joinTable
     * @param string $joinTableId
     * @param string $table
     * @param string $tableId
     * @return QueryBuilder
     */
    public function join(string $joinTable, string $joinTableId, string $table, string $tableId): QueryBuilder
    {
        $this->query .= " INNER JOIN $joinTable ON $joinTable.$joinTableId = $table.$tableId";

        return $this;
    }

    /**
     * @param string $joinTable
     * @param string $joinTableId
     * @param string $table
     * @param string $tableId
     * @return QueryBuilder
     */
    public function leftJoin(string $joinTable, string $joinTableId, string $table, string $tableId): QueryBuilder
    {
        $this->query .= " LEFT JOIN $joinTable ON $joinTable.$joinTableId = $table.$tableId";

        return $this;
    }

    /**
     * @param string|null $table
     * @return QueryBuilder
     
     */
    public function latest(?string $table = null): QueryBuilder
    {
        $table = $table ? $table . '.' : '';

        $this->query .= " ORDER BY $table created_at DESC";

        return $this;
    }

    /**
     * @param string $field
     * @param string $dir
     * @return QueryBuilder
     
     */
    public function orderBy(string $field, string $dir): QueryBuilder
    {
        $this->query .= " ORDER BY $field $dir";

        return $this;
    }

    /**
     * @return void 
     */
    public function bindParams(): void
    {
        foreach ($this->params as $param) {
            foreach ($param as $key => &$value) {
                $this->statement->bindParam($key, $value);
            }
        }

        $this->params = [];
    }

    /**
     * @return array 
     */
    public function get(): array
    {
        $this->prepareAndExecute();

        $results = $this->statement->fetchAll();

        $this->statement = null;
        $this->query = null;

        return $results;
    }

    /**
     * @return array|null
     */
    public function first(): array |null
    {
        $this->prepareAndExecute();

        $result = $this->statement->fetch();

        $this->statement = null;
        $this->query = null;

        return $result ? $result : null;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        $this->prepareAndExecute();

        $result = $this->statement->fetchColumn();

        $this->statement = null;
        $this->query = null;

        return $result ? $result : 0;
    }

    /**
     * @return void 
     */
    protected function prepareAndExecute(): void
    {
        if (isset($this->statement)) {
            $this->bindParams();
            $this->statement->execute();
        } else {
            $this->statement = $this->pdo->prepare($this->query);
            $this->statement->execute();
        }
    }

    /**
     * @param string $table
     * @param array $columns
     * @param string $search
     * @return array
     */
    public function search(string $table, array $columns, string $search): array
    {
        $placeholders = array_map(function ($column) {
            return ":$column";
        }, $columns);

        $query = "SELECT * FROM $table WHERE $columns[0] LIKE $placeholders[0]";

        if (count($columns) > 1) {
            $query .= " OR $columns[1] LIKE $placeholders[1]";
        }

        $statement = $this->pdo->prepare($query);

        $search = "%$search%";

        foreach ($placeholders as $placeholder) {
            $statement->bindParam($placeholder, $search);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param string $table
     * @param array  $data
     * @return bool
     */
    public function insert(string $table, array $data): bool
    {
        $keys = implode(', ', array_keys($data));

        $placeholders = array_map(function ($key) {
            return ':' . $key;
        }, array_keys($data));

        $placeholders = implode(', ', $placeholders);

        $query = "INSERT INTO $table($keys) VALUES($placeholders)";

        $statement = $this->pdo->prepare($query);

        foreach ($data as $key => &$value) {
            $statement->bindParam($key, $value);
        }

        return $statement->execute($data);
    }

    /**
     * @param string $table
     * @param array $data
     * @param int $id
     * @return
     */
    public function update(string $table, array $data, int $id)
    {
        unset($data['id']);
        $values = '';

        $query = "UPDATE $table SET ";

        foreach ($data as $key => $value) {
            $values .= "$key = :$key, ";
        }

        $values = rtrim($values, ', ');

        $query .= $values;

        $query .= " WHERE id = :id";

        $statement = $this->pdo->prepare($query);

        $statement->bindParam('id', $id);

        foreach ($data as $key => &$value) {
            $statement->bindParam($key, $value);
        }

        return $statement->execute();
    }

    /**
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @return array|bool
     */
    public function delete(string $table, string $field, mixed $value): array |bool
    {
        $query = "DELETE FROM $table WHERE $field = :$field";

        $statement = $this->pdo->prepare($query);
        $statement->bindParam(":$field", $value);

        return $statement->execute();
    }
}

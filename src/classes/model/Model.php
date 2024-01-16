<?php

// Gebaseerd op: http://codetuto.com/2013/07/creating-a-php-website-using-mvc-4-creating-model-class/
namespace Acme\classes\model;

use PDO;

/**
 * Model base class
 */
class Model
{

    protected static string $tableName = '';
    protected static string $primaryKey = '';
    protected array $columns;
    private Database $db;
    private int $lastInsertId;

    /**
     * Dependent on Database object
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * This method is used to give each column a value before an insert
     * or changed columns before an update
     * @param string $column
     * @param mixed $value
     * @return void
     */
    public function setColumnValue(string $column, mixed $value): void
    {
        if ($column === "identifier") {
            $column = static::$tableName . "_identifier";
        }

        $this->columns[$column] = $value;
    }

    /**
     * Gets the value from a column
     *
     * @param string $column
     * @return mixed
     */
    public function getColumnValue(string $column)
    {
        return $this->columns[$column];
    }

    /**
     * Insert OR update the item data in database
     *
     * @return bool true on succes, false on failure
     */
    public function insert(): bool
    {
        $query = "REPLACE INTO " . static::$tableName . " (" . implode(
                ",",
                array_keys(
                    $this->columns
                )
            ) . ") VALUES(";
        $keys = array();
        foreach ($this->columns as $key => $value) {
            $keys[":" . $key] = $value;
        }
        $query .= implode(",", array_keys($keys)) . ")";
        $s = $this->db->getPreparedStatement($query);
        $result = $s->execute($keys);
        $this->lastInsertId = $this->db->lastInsertId();
        return $result;
    }

    /**
     * Update the item data in database
     *
     * @return bool true on succes, false on failure
     */
    public function update(mixed $id): bool
    {
        $query = "UPDATE " . static::$tableName . " SET ";
        $keys = array();
        foreach ($this->columns as $key => $value) {
            $keys[":" . $key] = $value;
            $query .= $key . "=:" . $key . ",";
        }
        $keys[":id"] = $id;
        $query = rtrim($query, ",");
        $query .= " WHERE " . static::$primaryKey . "=:id LIMIT 1";
        $s = $this->db->getPreparedStatement($query);
        return $s->execute($keys);
    }

    /**
     * Delete item
     *
     * @param mixed $id
     * @return bool true on success and false on failure
     */
    public function delete(mixed $id): bool
    {
        $query = "DELETE FROM " . static::$tableName . " WHERE " . static::$primaryKey . "=:id LIMIT 1";
        $s = $this->db->getPreparedStatement($query);
        return $s->execute(array(':id' => $id));
    }

    /**
     * Create an instance of this Model from the database row
     *
     * @param array $column
     * @return void
     */
    private function createFromDb(array $column)
    {
        if ($column) {
            foreach ($column as $key => $value) {
                $this->columns[$key] = $value;
            }
        }
    }

    /**
     * Get all items
     * Conditions are combined by logical AND
     *
     * @return array<Model>
     * @example getAll(array(name=>'Bond',job=>'artist'),'age DESC',0,25) converts to SELECT * FROM TABLE WHERE name='Bond' AND job='artist' ORDER BY age DESC LIMIT 0,25
     *
     */
    public function getAll(
        $fields = "*",
        $condition = array(),
        $order = null,
        $startIndex = null,
        $count = null,
        $extraQuery
    ): array {
        $query = "SELECT {$fields} FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        $query .= " " . $extraQuery . " ";
        if ($order) {
            $query .= " ORDER BY " . $order;
        }
        if ($startIndex !== null) {
            $query .= " LIMIT " . $startIndex;
            if ($count) {
                $query .= "," . $count;
            }
        }
        return $this->get($query, $condition);
    }

    /**
     * Pass a custom query and condition
     *
     * @param string $query
     * @param array $condition
     * @return array<Model>
     * @example get('SELECT * FROM TABLE WHERE name=:user OR age<:age',array(name=>'Bond',age=>25))
     *
     */
    public function get(string $query, array $condition = array()): array
    {
        $s = $this->db->getPreparedStatement($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $result = $s->fetchAll(PDO::FETCH_ASSOC);

        $collection = array();
        $className = static::class; //get_called_class(); is deprecated
        foreach ($result as $row) {
            $item = new $className();
            $item->createFromDb($row);
            array_push($collection, $item);
        }
        return $collection;
    }

    /**
     * Get a single item
     *
     * @param $fields
     * @param $condition
     * @param $order
     * @param $startIndex
     * @return Model
     */
    public function getOne(
        $fields = "*",
        $condition = array(),
        $order = null,
        $startIndex = null
    ): Model {
        $query = "SELECT {$fields} FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        if ($order) {
            $query .= " ORDER BY " . $order;
        }
        if ($startIndex !== null) {
            $query .= " LIMIT " . $startIndex . ",1";
        }
        $s = $this->db->getPreparedStatement($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        $className = static::class; //get_called_class(); is deprecated
        $item = new $className();
        $item->createFromDb($row);
        return $item;
    }

    /**
     * Get an item by the primarykey
     *
     * @param $value
     * @return Model
     */
    public function getByPrimaryKey(mixed $value, $fields = "*"): Model
    {
        $condition = array();
        $condition[static::$primaryKey] = $value;
        return $this->getOne($fields, $condition);
    }

    /**
     * Get the number of items
     *
     * @param array $condition
     * @return int
     */
    public function getCount(array $condition = array()): int
    {
        $query = "SELECT COUNT(*) FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        $s = $this->db->getPreparedStatement($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $countArr = $s->fetch();
        return $countArr[0];
    }

    /**
     * Get an array with current data (key is columnname from table)
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the last inserted id (after insert)
     *
     * @return int
     */
    public function getLastInsertId(): int
    {
        return $this->lastInsertId;
    }
}
<?php

namespace Core\Traits;

use App\Enums\SQL;
use PDO;
use splitbrain\phpcli\Exception;

trait Queryable
{
    static protected ?string $tableName = null;

    static protected string $query = '';

    protected array $commands = [];

    static public function __callStatic(string $name, array $arguments)
    {
        if (in_array($name, ['where', 'join'])) {
            return call_user_func_array([new static, $name], $arguments);
        }

        throw new Exception('Method not allowed', 422);
    }

    public function __call(string $name, array $arguments)
    {
        if (in_array($name, ['where', 'join'])) {
            return call_user_func_array([$this, $name], $arguments);
        }

        throw new Exception('Method not allowed', 422);
    }

    static protected function resetQuery(): void
    {
        static::$query = '';
    }

    /**
     * User::select() - SELECT * FROM users
     * @param array $columns
     * @return static
     */
    static public function select(array $columns = ['*']): static
    {
        static::resetQuery();
        static::$query .= 'SELECT ' . implode(', ', $columns) . ' FROM ' . static::$tableName;

        $obj = new static;
        $obj->commands[] = 'select';

        return $obj;
    }

    # User::find() => User object
    static public function find(int $id): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE id = :id");
        $query->bindParam('id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    # User::find() => User object
    static public function findBy(string $column, mixed $value): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE $column = :$column");
        $query->bindParam($column, $value, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    # User::all()
    static public function all(): array
    {
        return static::select()->get();
    }

    /**
     * @param array $fields = ['email' => '.,.', 'password' => '....']
     * @return bool
     */
    static public function create(array $fields): bool
    {
        # ['placeholders', 'keys']\
        $params = static::prepareCreateParams($fields);
        $query = db()->prepare("INSERT INTO " . static::$tableName . " ($params[keys]) VALUES ($params[placeholders]);");

        return $query->execute($fields);
    }

    static public function createAndReturn(array $fields): null|static
    {
        static::create($fields);

        return static::find(db()->lastInsertId());
    }

    static protected function prepareCreateParams(array $fields): array
    {
        $keys = array_keys($fields);
        $placeholders = preg_filter('/^/', ':', $keys);
        /**
         * 'keys' => ['name'],
         * 'placeholders' => [':name']
         */
        return [
            'keys' => implode(', ', $keys),
            'placeholders' => implode(', ', $placeholders),
        ];
    }


    # User::delete(2)
    static public function delete(int $id): bool
    {
        $query = db()->prepare("DELETE FROM " . static::$tableName . " WHERE id = :id");
        $query->bindParam('id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    # $user->destroy()
    public function destroy(): bool
    {
        return static::delete($this->id);
    }

    public function update(array $fields): static
    {
        $query = db()->prepare("UPDATE " . static::$tableName . " SET " . $this->updatePlaceholders($fields) . ' WHERE id = :id');

        $fields['id'] = $this->id;
        $query->execute($fields);

        return static::find($this->id);
    }

    // User::select()->get() => SELECT * FROM users
    public function get(): array
    {
        return db()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public function toSql(): string
    {
        return static::$query;
    }

    public function and(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->required(['where'], 'AND can not be used without');

        static::$query .= ' AND';
        $this->commands[] = 'and';

        return $this->where($column, $operator, $value);
    }

    public function or(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->required(['where'], 'OR can not be used without');

        static::$query .= ' OR';
        $this->commands[] = 'or';

        return $this->where($column, $operator, $value);
    }

    public function exists(): bool
    {
        $this->required(['select'], 'Method exists() can not be called without');
        return !empty($this->get());
    }

    /**
     * @param string $table
     * @param array $conditions = [
     *  [
     *      'left' => name
     *      'operator' => =
     *      'right' => product.name
     *  ],
     *   [
     *       'left' => name
     *       'operator' => =
     *       'right' => product.name
     *   ]
     * ]
     * @param string $type
     * @return $this
     */
    protected function join(string $table, array $conditions, string $type = 'LEFT'): static
    {
        $obj = in_array('select', $this->commands) ? $this : static::select();

        $obj->required(['select'], 'JOIN can not be called without');

        $obj->commands[] = 'join';

        $conditions = array_map(fn ($condArr) => "$condArr[left] $condArr[operator] $condArr[right]", $conditions);

        static::$query .= " $type JOIN $table ON " . implode(', ', $conditions);

        return $obj;
    }

    /**
     * @param array $columns = [
     *  'name' => 'ASC',
     *  'date' => 'DESC'
     * ]
     * @return $this
     */
    public function orderBy(array $columns): static
    {
        $this->required(['select'], 'ORDER BY can not be called without');

        $this->commands[] = 'order';

        static::$query .= " ORDER BY ";

        $lastKey = array_key_last($columns);

        foreach ($columns as $column => $direction) {
            static::$query .= $column . ' ' . $direction . ($column === $lastKey ? '' : ', ');
        }

        return $this;
    }

    # User::select()->where()->and()->or()
    # User::where()
    protected function where(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        # check if we can use where
        $this->prevent(['order', 'limit', 'having', 'group'], 'WHERE can not be used after');
        # select
        $obj = in_array('select', $this->commands) ? $this : static::select();

        $value = $this->transformWhereValue($value);

        if (!in_array('where', $obj->commands)) {
            static::$query .= ' WHERE';
            $obj->commands[] = 'where';
        }

        static::$query .= " $column $operator->value $value";

        return $obj;
    }

    protected function transformWhereValue(mixed $value): string|int|float
    {
        $checkOnString = fn ($v) => !is_null($v) && !is_bool($v) && !is_numeric($v) && !is_array($v) && $v !== SQL::NULL->value;

        if ($checkOnString($value)) {
            $value = "'$value'";
        }

        if (is_null($value)) {
            $value = SQL::NULL->value;
        }

        if (is_array($value)) {
            $value = array_map(fn ($v) => $checkOnString($v) ? "'$v'" : $v, $value);
            $value = '(' . implode(', ', $value) . ')'; # (1, NULL, 'string')
        }

        if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }

        return $value;
    }

    protected function prevent(array $preventCommands, string $message = ''): void
    {
        foreach ($preventCommands as $command) {
            if (in_array($command, $this->commands)) {
                $message = sprintf(
                    '%s: %s [%s]',
                    static::class,
                    $message,
                    $command
                );
                throw new Exception($message, 422);
            }
        }
    }

    protected function required(array $requiredCommands, string $message = ''): void
    {
        foreach ($requiredCommands as $command) {
            if (!in_array($command, $this->commands)) {
                $message = sprintf(
                    '%s: %s [%s]',
                    static::class,
                    $message,
                    $command
                );
                throw new Exception($message, 422);
            }
        }
    }

    protected function updatePlaceholders(array $fields): string
    {
        $keys = array_map(fn ($key) => "$key = :$key", array_keys($fields));

        return implode(', ', $keys);
    }
}

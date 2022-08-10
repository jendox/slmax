<?php
declare(strict_types=1);

/**
 * Класс для формирования и исполнения запросов к базе данных
 * Принимает название таблицы
 * После исполнения execute() возвращает результат запроса к базе данных
 */
abstract class QueryBuilder
{
    private mysqli $mysqli;
    private string $table_name;
    private string $query = '';
    private array $values = [];

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        $this->mysqli = (new Database())->get();
    }

    public function select(array $columns): static
    {
        $this->query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $this->table_name;
        return $this;
    }

    public function where(string $column, string $operator, int $value): static
    {
        $this->query .= ' WHERE ' . $column . ' ' . $operator . ' ?';
        $this->values = [];
        $this->values[] = $value;

        return $this;
    }

    public function insert(array $columns, array $values): static
    {
        $this->query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table_name,
            implode(',', $columns),
            implode(',', array_fill(0, count($values), '?'))
        );
        $this->values = $values;
        return $this;
    }

    public function delete(): static
    {
        $this->query = 'DELETE FROM ' . $this->table_name;
        return $this;
    }

    /**
     * Функция подготовки и исполнения сформированного запроса к базе данных
     * @return bool|mysqli_result
     */
    public function execute(): bool|mysqli_result
    {
        $stmt = $this->mysqli->prepare($this->query);
        if (count($this->values)) {
            $params = [];
            $types = array_reduce($this->values, function ($string, &$arg) use (&$params) {
                $params[] = &$arg;
                if (is_float($arg)) $string .= 'd';
                elseif (is_integer($arg)) $string .= 'i';
                elseif (is_string($arg)) $string .= 's';
                else                        $string .= 'b';
                return $string;
            }, '');
            array_unshift($params, $types);
            call_user_func_array([$stmt, 'bind_param'], $params);
        }
        if ($result = $stmt->execute()) {
            if (str_starts_with($this->query, 'SELECT')) {
                $result = $stmt->get_result();
            }
        }
        $stmt->close();
        return $result;
    }
}
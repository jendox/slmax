<?php
declare(strict_types=1);

if (!class_exists('Person', true)) {
    throw new LogicException('Unable to load class Person');
}

/**
 * Класс для работы со списками людей
 * Создает массив id людей по заданному условию,
 * формирует массив экземпляров класса Person,
 * удаляет полученный массив экземпляров
 */
class PersonList extends QueryBuilder
{
    private array $ids = [];

    public function __construct(string $operator, int $value)
    {
        parent::__construct('person');
        $result = $this->select(['id'])->where('id', $operator, $value)->execute();
        while ($row = $result->fetch_assoc()) {
            $this->ids[] = $row['id'];
        }
    }

    public function get(): array
    {
        $list = [];
        foreach ($this->ids as $id) $list[] = new Person(['id' => $id]);
        return $list;
    }

    public function remove(): void
    {
        foreach ($this->get() as $person) $person->remove();
    }
}
<?php
declare(strict_types=1);

/**
 * Класс для работы с базой данных людей
 * Создает человека в БД с заданной информацией, либо берет имеющуюся
 * в БД информацию по id
 */
class Person extends QueryBuilder
{
    private int $id;
    private int $gender;
    private string $firstName;
    private string $lastName;
    private string $birthDate;
    private string $birthPlace;

    public function __construct(array $props)
    {
        parent::__construct('person');
        $extPerson = $this->getById($props['id']);
        if ($extPerson != null) {
            $this->initProperties($extPerson);
        } else {
            if ($this->validate($props)) {
                $this->initProperties($props);
                $this->save();
            } else {
                throw new Exception('Invalid data!');
            }
        }
    }

    private function getById(int $id): bool|array|null
    {
        $result = $this->select(['*'])->where('id', '=', $id)->execute();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Функция валидации входящих данных
     * @param array $props
     * @return bool
     */
    private function validate(array $props): bool
    {
        return ctype_alpha($props['firstname'])
               && ctype_alpha($props['lastname']);
    }

    /**
     * Функция инициализации свойств класса
     * @param array $props
     * @return void
     */
    private function initProperties(array $props): void
    {
        $this->id = $props['id'];
        $this->firstName = $props['firstname'];
        $this->lastName = $props['lastname'];
        $this->birthDate = $props['birthdate'];
        $this->gender = $props['gender'];
        $this->birthPlace = $props['birthplace'];
    }

    public function save(): mysqli_result|bool
    {
        return $this
            ->insert(
                [
                    'id',
                    'firstname',
                    'lastname',
                    'birthdate',
                    'gender',
                    'birthplace'
                ],
                [
                    $this->id,
                    $this->firstName,
                    $this->lastName,
                    $this->birthDate,
                    $this->gender,
                    $this->birthPlace
                ]
            )
            ->execute();
    }

    public function remove(): mysqli_result|bool
    {
        return $this->delete()->where('id', '=', $this->id)->execute();
    }

    static function getAge(string $birthDate): int
    {
        $age = date_diff(date_create($birthDate), date_create(date('d-m-Y')));
        return $age->y;
    }

    static function getGenderString(int $gender): string
    {
        return $gender ? 'муж' : 'жен';
    }

    public function formatPerson(string ...$params): stdClass
    {
        $newPerson = new stdClass();
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            $newPerson->$key = $value;
        }
        foreach ($params as $param) {
            if ($param == 'age') {
                $newPerson->$param = Person::getAge($this->birthDate);
            }
        }
        return $newPerson;
    }
}
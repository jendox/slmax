<?php
declare(strict_types=1);

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

try {
    $person = new Person([
        'id' => 1,
        'firstname' => 'Ivan',
        'lastname' => 'Ivanov',
        'gender' => 1,
        'birthdate' => '1990-01-01',
        'birthplace' => 'g. Minsk',
    ]);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    $person = new Person([
        'id' => 2,
        'firstname' => 'Nina',
        'lastname' => 'Petrova',
        'gender' => 0,
        'birthdate' => '1991-04-01',
        'birthplace' => 'd. Graevka',
    ]);
} catch (Exception $e) {
    echo $e->getMessage();
}

$list = new PersonList('>', 1);
var_dump($list->get());
$list->remove();
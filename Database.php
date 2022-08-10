<?php
declare(strict_types=1);
require_once 'config.php';

/**
 * Класс для работы с MySQL
 * Берет настройки из файла config.php
 * Открывает соединение с базой данных
 */
class Database
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_ROOT_PASSWORD, MYSQL_DATABASE);
        if ($this->db->connect_error) {
            die('Connection failed: ' . $this->db->connect_error);
        }
    }

    public function get(): mysqli
    {
        return $this->db;
    }
}
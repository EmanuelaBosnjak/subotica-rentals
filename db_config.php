<?php

define('DB', array(
    'host' => 'localhost',
    'username' => 'nda',
    'password' => 'BesZKhwcizViY7s',
    'database' => 'nda'
));

try {
    $dbh = new PDO(
        "mysql:host=" . DB['host'] . ";dbname=" . DB['database'],
        DB['username'],
        DB['password'],
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"]
    );
} catch (PDOException $e) {
    exit("DB Connect Error: " . $e->getMessage());
}

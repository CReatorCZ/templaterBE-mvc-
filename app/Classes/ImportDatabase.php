<?php

namespace App\Classes;

use Nette\Database\Connection;

class ImportDatabase
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function setDatabase(array $data)
    {
        $this->database->query("DELETE FROM forms");

        unset($data[1]);
        unset($data[0]);
        foreach ($data as $row) {
            $this->database->query("INSERT INTO forms (firstName, email, age, day, id) 
                                        VALUES (?,?,?,?,?)",$row[0],$row[1],$row[2],$row[3],$row[4]);
        }


    }
}
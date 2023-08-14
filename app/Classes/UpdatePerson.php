<?php

namespace App\Classes;

use Nette\Application\UI\Form;
use Nette\Database\Connection;
use Nette\Database\ResultSet;
use Nette\Database\Row;

class UpdatePerson
{
    //$row = ['name' => 'John', 'age' => '33', /* ... */];
//$form->setDefaults($row);

    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }


    public function getPerson(int $id): ?Row{
        $result = $this->database->query("SELECT * FROM forms WHERE id = ?", $id);
        return $result->fetch();
    }
    public function updatePerson(int $id, $data): void
    {
        $this->database->query(
            'UPDATE forms SET firstName = ?, email = ?, age = ?, day = ? WHERE id = ?',
            $data->name,
            $data->email,
            $data->age,
            $data->day,
            $id
        );
    }
}
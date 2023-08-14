<?php

namespace App\Classes;

use Nette\Database\Connection;

class DeletePerson
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function deletePerson(int $id): void {
        $this->database->query("DELETE FROM forms WHERE id = ?", $id);
    }

}
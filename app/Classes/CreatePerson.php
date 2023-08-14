<?php

namespace App\Classes;

use Nette\Database\Connection;

class CreatePerson
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database= $database;
    }
//(firstName, age, day) VALUES ('$data->name',$data->age,'$data->day')
    public function setPerson($data): void {
        $this->database->query("INSERT INTO forms ?", [
            'firstName' => $data->name,
            'email' => $data->email,
            'age' => $data->age,
            'day' => $data->day
        ]);
    }

}
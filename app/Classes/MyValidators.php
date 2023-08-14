<?php

namespace App\Classes;

use Nette\Database\Connection;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextInput;

class MyValidators
{
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function validateEmailDuplicity(TextInput $control, ?int $id): bool {

        $email = $control->getValue();
        $query = "SELECT * FROM forms WHERE email = ?";

        if ($id !== null){
            $query .=" AND id != ?";
            $existingEmail = $this->database->query($query, $email, $id)->fetch();
        } else {
            $existingEmail = $this->database->query($query, $email)->fetch();
        }



        return !($existingEmail);
    }

}
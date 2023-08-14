<?php

namespace App\Classes;

use Nette\Database\Connection;
use Nette\Database\ResultSet;

class GetList
{
    private $database;
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function getList(): ResultSet {
        $result = $this->database->query('SELECT * FROM forms ');
        return $result;
    }

}
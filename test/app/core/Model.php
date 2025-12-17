<?php

require_once '../app/config/db.php';

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

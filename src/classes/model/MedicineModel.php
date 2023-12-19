<?php

namespace Acme\classes\model;


class MedicineModel extends Model
{

    protected static string $tableName = "Medicine";
    protected static string $primaryKey = "medicine_identifier";

    public function __construct()
    {
        parent::__construct(Database::getInstance());
    }

}
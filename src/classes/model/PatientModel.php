<?php

namespace Acme\classes\model;


class PatientModel extends Model
{

    protected static string $tableName = "patient";
    protected static string $primaryKey = "patient_identifier";

    public function __construct()
    {
        parent::__construct(Database::getInstance());
    }

}
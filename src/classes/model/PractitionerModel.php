<?php

namespace Acme\classes\model;


class PractitionerModel extends Model
{

    protected static string $tableName = "practitioner";
    protected static string $primaryKey = "practitioner_identifier";

    public function __construct()
    {
        parent::__construct(Database::getInstance());
    }

}
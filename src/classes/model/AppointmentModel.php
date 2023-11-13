<?php

namespace Acme\classes\model;

class AppointmentModel extends Model
{

    protected static string $tableName = "appointment";
    protected static string $primaryKey = "appointment_identifier";

    public function __construct()
    {
        parent::__construct(Database::getInstance());
    }

    public function getAll($fields = "*", $condition = array(), $order = null, $startIndex = null, $count = null): array
    {
        // TODO: Resultaten aanvullen met patientgegevens uit patienttabel

        $results = parent::getAll($fields, $condition, $order, $startIndex, $count);

        return $results;
    }

}
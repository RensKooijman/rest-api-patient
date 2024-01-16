<?php

namespace Acme\classes\model;

use Acme\classes\model\PatientModel;
class AppointmentModel extends Model
{

    protected static string $tableName = "appointment";
    protected static string $primaryKey = "appointment_identifier";

    public function __construct()
    {
        parent::__construct(Database::getInstance());
    }

    public function getAll($fields = "*", $condition = array(), $order = null, $startIndex = null, $count = null, $extraQuery = null): array
    {
        $extraQuery = "INNER JOIN patient ON appointment.patient_identifier = patient.patient_identifier";
        $results = parent::getAll($fields, $condition, $order, $startIndex, $count, $extraQuery);

        return $results;
    }

}
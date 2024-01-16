<?php

namespace Acme\tests;

require_once( __DIR__ . "/../../vendor/autoload.php");

use Acme\classes\model\DotEnv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\BadHeaderException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class test extends TestCase
{

    private function getGUID(): string
    {
        // Unieke string maken (dat heet een guid: global unique identifier)
        // Dit wordt de identifier in een tabel (soort van primary key)
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    private function URLTestsProvider(): array
    {

        $url = $_ENV['DOC_ROOT'];

        // Url: In de querystring de resource opgeven en de identifier:
        // bijvoorbeeld een patient en hoe deze is te herkennen in het systeem (niet per se de tabel en primary key!)
        // De resource heeft een overeenkomstige class. Als de resource patient is, dan is dat patientModel.

        $patient_identifier = $this->getGUID();
        $practitioner_identifier = $this->getGUID();
        $appointment_identifier = $this->getGUID();
        $medicine_identifier = $this->getGUID();

        return [
            [
                "test" => "Voeg een nieuwe patient toe aan het systeem",
                "url" => $url . "?resource=patient&identifier=$patient_identifier",
                "method" => "post",
                "data" => "name=Piet&telecom=1234567890&birthDate=2019-01-01 04:00:01&address=Ergens 1 Lutjebroek Nederland&photo=",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Haal specifieke gegevens van een specifieke patient op",
                "url" => $url . "?resource=patient&identifier=$patient_identifier&fields=name,patient_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"name":"Piet","patient_identifier":"' . $patient_identifier . '"}',
                "check" => 'equals'
            ],
            [
                "test" => "Haal specifieke gegevens van alle patienten op",
                "url" => $url . "?resource=patient&fields=name,patient_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"name":"Piet","patient_identifier":"' . $patient_identifier . '"}',
                "check" => 'contains'
                // When no identifier is given, then an array is returned with all possible result. In that case, use 'contains'
            ],
            [
                "test" => "Wijzig een gegeven van een specifieke patient",
                "url" => $url . "?resource=patient&identifier=$patient_identifier",
                "method" => "put",
                "data" => "name=Jan",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Voeg een nieuwe zorgverlener (practitioner) toe aan het systeem",
                "url" => $url . "?resource=practitioner&identifier=$practitioner_identifier",
                "method" => "post",
                "data" => "name=Jan&telecom=0987654321&birthDate=1995-08-21 00:00:00&address=Daar 2 Broek in Waterland Nederland&photo=",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Voeg een nieuwe afspraak tussen een bestaande patient en een zorgverlener",
                "url" => $url . "?resource=appointment&identifier=$appointment_identifier",
                "method" => "post",
                "data" => "practitioner_identifier=$practitioner_identifier&patient_identifier=$patient_identifier&status=proposed",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Haal alle afspraken op van een specifieke patient",
                "url" => $url . "?resource=appointment&fields=status,end&patient_identifier=$patient_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"status":"proposed","end":"0000-00-00 00:00:00"}',
                "check" => 'contains'
            ],
            [
                "test" => "Haal alle afspraken op van een specifieke patient gesorteerd op datum (oplopend)",
                "url" => $url . "?resource=appointment&fields=status,end&sort=start&patient_identifier=$patient_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"status":"proposed","end":"0000-00-00 00:00:00"}',
                "check" => 'contains'
            ],
            [
                "test" => "Haal alle afspraken op van een specifieke zorgverlener",
                "url" => $url . "?resource=appointment&fields=status,end&practitioner_identifier=$practitioner_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"status":"proposed","end":"0000-00-00 00:00:00"}',
                "check" => 'contains'
            ],
            [
                "test" => "Voeg een nieuwe samenvatting toe aan een afspraak",
                "url" => $url . "?resource=appointment&identifier=$appointment_identifier",
                "method" => "put",
                "data" => "summary=Heeft moeite met ademen.",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Haal alle samenvattingen uit het dossier van een patient op",
                "url" => $url . "?resource=appointment&fields=summary&patient_identifier=$patient_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"summary":"Heeft moeite met ademen."}',
                "check" => 'contains'
            ],
            [
                "test" => "Haal alle samenvattingen uit het dossier van een patient op bij een bepaalde zorgverlener",
                "url" => $url . "?resource=appointment&fields=summary&patient_identifier=$patient_identifier&practitioner_identifier=$practitioner_identifier",
                "method" => "get",
                "data" => "",
                "result" => '{"summary":"Heeft moeite met ademen."}',
                "check" => 'contains'
            ],
            [
                "test" => "Schrijf een medicijn voor aan een patient door een bepaalde zorgverlener",
                "url" => $url . "?resource=medicine&identifier=$medicine_identifier",
                "method" => "post",
                "data" => "patient_identifier=$patient_identifier&practitioner_identifier=$practitioner_identifier&name=paracetamol&howtouse=driemaal daags&amount=50",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "De zorgverlener annuleert een afspraak met een bepaalde patient",
                "url" => $url . "?resource=appointment&identifier=$appointment_identifier",
                "method" => "put",
                "data" => "cancelationreason=echt geen tijd&status=cancelled",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Verwijder een specifieke afspraak",
                "url" => $url . "?resource=appointment&identifier=$appointment_identifier",
                "method" => "delete",
                "data" => "",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Verwijder een specifieke medicein toewijzing",
                "url" => $url . "?resource=medicine&identifier=$medicine_identifier",
                "method" => "delete",
                "data" => "",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Verwijder een specifieke patient",
                "url" => $url . "?resource=patient&identifier=$patient_identifier",
                "method" => "delete",
                "data" => "",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ],
            [
                "test" => "Verwijder een specifieke zorgverlener",
                "url" => $url . "?resource=practitioner&identifier=$practitioner_identifier",
                "method" => "delete",
                "data" => "",
                "result" => '{"result":"1"}',
                "check" => 'equals'
            ]
        ];
    }

    /**
     * @dataProvider URLTestsProvider
     * @throws GuzzleException
     */
    public function urlTest(array $tester)
    {
        extract($tester); //keys worden variabelen met value
        if (!in_array(strtolower($method), ['post', 'get', 'put', 'delete', 'options', 'head'])) {
            self::throwException(new BadHeaderException());
        }

        $client = new Client();
        $className = __NAMESPACE__ . '\\' . "TestRequest" . ucfirst(strtolower($method));
        $obj = new $className();
        $request = $obj->getRequest($client, $url, $data);

        // Send an asynchronous request.
        $promise = $client->sendAsync($request)->then(function ($response) use ($test, $result, $method, $url, $check) {
            // PHPUnit tests to see if returned value match expected value
            $body = (string)$response->getBody();
            try {
                echo "<h2>Test: " . $test . "</h2>";
                $method = strtoupper($method);
                if ($check === 'equals') {
                    $this->assertEquals($result, $body);
                } elseif ($check === 'contains') {
                    $this->assertContains(json_decode($result, true), json_decode($body, true));
                }
                echo "<p>OK</p>";
            } catch (ExpectationFailedException $err) {
                echo "<p>ERROR</p>";
                echo  $err->getMessage();
                echo "<p>$method on $url<br>Returned value: $body<br>Expected: $result</p>";
            } finally {
                echo "<hr>";
            }
        });
        $promise->wait(); 
    }

    /**
     * @throws GuzzleException
     */
    public function test(): void
    {
        foreach ($this->URLTestsProvider() as $tests) {
            $this->urlTest($tests);
        }
    }
}

const ENV_PATH = __DIR__ . '../../../.env';
$env = new DotEnv(ENV_PATH);
$env->load();

$x = new test();
try {
    $x->Test();
} catch (GuzzleException $e) {
    echo $e->getMessage();
}

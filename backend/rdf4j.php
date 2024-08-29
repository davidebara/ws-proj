<?php
header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

require '../vendor/autoload.php';

$client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafexamen");
$interogare = "SELECT * WHERE {?x ?y ?z}";
$rezultate = $client->query($interogare);
$tableString = $rezultate->__toString();

function parseString($inputString)
{
    $lines = explode("\n", $inputString);
    $data = array();

    foreach ($lines as $line) {
        $line = trim(trim($line, '|'));

        if (preg_match('/^[\+\-]+$/', $line)) continue;
        if (preg_match('/^\?/', $line)) continue;

        $cells = explode('|', $line);

        $cells = array_map('trim', $cells);
        $cells = array_filter($cells);

        if (!empty($cells)) {
            $rowData = array(
                'x' => trim($cells[0]),
                'y' => trim($cells[1]),
                'z' => trim($cells[2])
            );
            $data[] = $rowData;
        }
    }

    return $data;
}

$result = parseString($tableString);
$jsonArray = json_encode($result);

$events = array();
$countries = array();

foreach ($result as $item) {
    $x = $item['x'];
    $y = substr($item['y'], strpos($item['y'], ':') + 1);
    $z = trim($item['z'], '"');

    if ($y === 'type' && $z === 'schema:Event') {
        $events[$x]['id'] = $x;
    } elseif ($y === 'type' && $z === 'schema:Country') {
        $countries[$x]['id'] = $x;
        $countries[$x]['events'] = [];
    } else {
        if (isset($events[$x])) {
            $events[$x][$y] = $z;
        } elseif (isset($countries[$x])) {
            if ($y === 'event') {
                $countries[$x]['events'][] = $z;
            } else {
                $countries[$x][$y] = $z;
            }
        }
    }
}

$belligerents = [];

foreach ($countries as $country) {
    foreach ($country['events'] as $eventId) {
        if (isset($events[$eventId])) {
            $eventName = $events[$eventId]['name'];
            $startDate = $events[$eventId]['startDate'];
            $endDate = $events[$eventId]['endDate'];

            $countryId = $country['id'];
            $countryName = $country['name'];
            $countryUrl = $country['url'];

            $eventNameFormat = str_replace(' ', '_', $events[$eventId]['name']);
            $belligerentKey = "{$eventNameFormat}-{$countryName}";

            $belligerents[$belligerentKey] = [
                'eventId' => $eventId,
                'endDate' => $endDate,
                'eventName' => $eventName,
                'startDate' => $startDate,
                'countryId' => $countryId,
                'countryName' => $countryName,
                'countryUrl' => $countryUrl
            ];
        }
    }
}

$belligerentsJson = json_encode($belligerents);
print($belligerentsJson);

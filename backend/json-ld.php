<?php
header("Access-Control-Allow-Origin: *");
require '../vendor/autoload.php';
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();

$response = $client->request('GET', 'https://websemantic.netlify.app/');
$cerere = $response->getContent();
procesareRaspuns($cerere);

function procesareRaspuns($raspunsHTTP)
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($raspunsHTTP);
    $docInterogabil = new DOMXPath($doc);
    $rezultate = $docInterogabil->query("/html/head/script[@type='application/ld+json']");
    $jsonld = json_decode($rezultate[0]->nodeValue, true);
    $events = [];
    $countries = [];

    foreach ($jsonld['@graph'] as $item) {
        if ($item['@type'] === 'Event') {
            $events[$item['@id']] = [
                'id' => $item['@id'],
                'name' => $item['name'],
                'startDate' => $item['startDate'],
                'endDate' => $item['endDate']
            ];
        }
    }

    foreach ($jsonld['@graph'] as $item) {
        if ($item['@type'] === 'Country') {
            $eventIds = is_array($item['event']) ? $item['event'] : [$item['event']];
            $eventIds = array_map(function ($event) {
                return is_array($event) ? $event['@id'] : $event;
            }, $eventIds);
            foreach ($eventIds as $eventId) {
                if (isset($events[$eventId])) {
                    $countries[] = [
                        'eventId' => $eventId,
                        'event' => $events[$eventId]['name'],
                        'startDate' => $events[$eventId]['startDate'],
                        'endDate' => $events[$eventId]['endDate'],
                        'country' => $item['name'],
                        'countryId' => $item['@id'],
                        'countryUrl' => $item['url']['@id']
                    ];
                }
            }
        }
    }

    print json_encode($countries);
}
?>
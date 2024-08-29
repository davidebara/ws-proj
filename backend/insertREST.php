<?php
require '../vendor/autoload.php';
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();
$response = $client->request('GET', "http://localhost/proiectWS/backend/rdf4j.php");
$json_data = $response->getContent();

$data = json_decode($json_data, true);

$wars = [];
$countries = [];
$belligerents = [];

foreach ($data as $key => $entry) {
    $war_id = $entry['eventId'];
    $country_id = $entry['countryId'];

    $wars[$war_id] = [
        'id' => $war_id,
        'name' => $entry['eventName'],
        'startDate' => $entry['startDate'],
        'endDate' => $entry['endDate'],
        'url' => '',
    ];

    $countries[$country_id] = [
        'id' => $country_id,
        'name' => $entry['countryName'],
        'url' => $entry['countryUrl'],
    ];

    $belligerents[] = ['war_id' => $war_id, 'country_id' => $country_id];
}

$wars = array_values($wars);
$countries = array_values($countries);

foreach ($wars as $war) {
    $response = $client->request('POST', "http://localhost:4000/wars", [
        'json' => [
            'eventId' => $war['id'],
            'warName' => $war['name'],
            'startDate' => $war['startDate'],
            'endDate' => $war['endDate'],
            'url' => $war['url'],
        ]
    ]);
    $response->getContent();
}
foreach ($countries as $country) {
    $response = $client->request('POST', "http://localhost:4000/countries", [
        'json' => [
            'countryId' => $country['id'],
            'countryName' => $country['name'],
            'url' => $country['url'],
        ]
    ]);
    $response->getContent();
}
foreach ($belligerents as $belligerent) {
    $response = $client->request('POST', "http://localhost:4000/belligerents", [
        'json' => [
            'eventId' => $belligerent['war_id'],
            'countryId' => $belligerent['country_id'],
        ]
    ]);
    $response->getContent();
}
echo "🪖 Date inserate pe serverul JSON cu succes"
?>
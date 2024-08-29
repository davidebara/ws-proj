<?php
require '../vendor/autoload.php';
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();

$response = $client->request('GET', 'http://localhost:4000/wars');
$warsJson = $response->getContent();

$response = $client->request('GET', 'http://localhost:4000/countries');
$countriesJson = $response->getContent();

$response = $client->request('GET', 'http://localhost:4000/belligerents');
$belligerentsJson = $response->getContent();

$wars = json_decode($warsJson, true);
$countries = json_decode($countriesJson, true);
$belligerents = json_decode($belligerentsJson, true);

$warsById = [];
$countriesById = [];

foreach ($wars as $war) {
    $warsById[$war['eventId']] = $war;
}

foreach ($countries as $country) {
    $countriesById[$country['countryId']] = $country;
}

$countryWarMap = [];

foreach ($belligerents as $belligerent) {
    $war = $warsById[$belligerent['eventId']];
    $country = $countriesById[$belligerent['countryId']];

    if (!isset($countryWarMap[$country['countryName']])) {
        $countryWarMap[$country['countryName']] = [];
    }

    $countryWarMap[$country['countryName']][] = $war;
}

echo "<table>";
echo "<tr><th>Event (War)</th><th>Start Date</th><th>End Date</th><th>Country</th><th>Country URL</th></tr>";

foreach ($countryWarMap as $countryName => $wars) {
    foreach ($wars as $war) {
        echo "<tr>";
        echo "<td>{$war['warName']}</td>";
        echo "<td>{$war['startDate']}</td>";
        echo "<td>{$war['endDate']}</td>";
        echo "<td>{$countryName}</td>";
        echo "<td><a href='{$countriesById[$belligerent['countryId']]['url']}' target=_blank>Wiki</a></td>";
        echo "</tr>";
    }
}

echo "</table>";
?>
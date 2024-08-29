<?php
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
    
    print json_encode($jsonld);
}
?>
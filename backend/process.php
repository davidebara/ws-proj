<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

require_once '../vendor/autoload.php';

use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use EasyRdf\Parser\JsonLd;
use EasyRdf\Serialiser\Turtle;
use Symfony\Component\HttpClient\HttpClient;

$obiectPHP = $_POST;

$client = HttpClient::create();
$url = 'http://localhost/proiectWS/backend/json-ld-raw.php';
$response = $client->request('GET', $url);
$jsonText = $response->getContent();

function parseJsonToGraph($jsonText) {
    $graph = new Graph();

    $parser = new JsonLd();

    $prefixe = new RdfNamespace();
    $prefixe->set("kg","http://g.co/kg/m/");
    $prefixe->set("dbr","http://dbpedia.org/page/");

    $baseUri = 'http://schema.org/';

    $parser->parse($graph, $jsonText, 'jsonld', $baseUri);

    return $graph;
}

$graph = parseJsonToGraph($jsonText);

$countryId = "kg:" . $obiectPHP["countryId"];
$graph->add($countryId, "a", new EasyRdf\Resource("schema:Country"));
$graph->addResource($countryId, "schema:event", $obiectPHP["eventId"]);
$graph->add($countryId, "schema:name", $obiectPHP["country"]);
$graph->addResource($countryId, "schema:sameAs", "dbr:" . $obiectPHP["country"]);
$graph->addResource($countryId, "schema:url", $obiectPHP["countryUrl"]);

$turtleSerializer = new Turtle();
$turtleData = $turtleSerializer->serialise($graph, 'turtle');

$client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafexamen/statements");
$grafDeTrimis = new EasyRdf\Graph();
$grafDeTrimis->parse($turtleData,"turtle");
print $client->insert($grafDeTrimis);
?>
<?php

require '../vendor/autoload.php';

use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();
$url = 'http://localhost/proiectWS/backend/rdf4j.php';
$response = $client->request('GET', $url);
$projectDataset = $response->getContent();

$open_ai_key = 'INSERT_API_KEY';
$open_ai = new OpenAi($open_ai_key);


$complete = $open_ai->chat([
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            "role" => "system",
            "content" => "You are a helpful assistant."
        ],
        [
            "role" => "user",
            "content" => "Scrie o poveste despre conflictele din istoria umanității, folosind datele din următorul fișier JSON:" . "<br>" .  $projectDataset . "<br>" . "Povestea trebuie să integreze evenimentele și țările menționate în fișierul JSON. Fă o poveste captivantă, cu detalii realiste și dramatism. Asigură-te că descrii contextul istoric și impactul fiecărui conflict asupra tuturor țărilor implicate. Ține cont de ordinea evenimentelor, a căror început și final sunt indicate de câmpurile startDate și endDate. Fișierul JSON conține linkuri către paginile Wikipedia pentru fiecare țară, pe care le poți folosi pentru a obține mai multe informații. Folosește multe emoticoane pentru a face povestea mai ușor de citit de către un om și evită formatarea textului în sintaxa Markdown. Succes!",
        ],
    ],
    'temperature' => 1.0,
    'max_tokens' => 1000,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
 ]);

$response = json_decode($complete, true);
$response = $response["choices"][0]["message"]["content"];
echo $response;
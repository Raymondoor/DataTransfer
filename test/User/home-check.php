<?php declare(strict_types=1);
/**
 * Example of what belongs in test/User: a hand-written, disposable check,
 * same spirit as test/Agent but written by a person rather than an agent -
 * not a PHPUnit test either. Run directly: `php test/User/home-check.php`.
 * Requires an actual webserver serving this project at HOME_URL.
 */
require_once __DIR__.'/../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$client = new Client();

try{
    $response = $client->get(HOME_URL);
    $status = $response->getStatusCode();
    $body = (string)$response->getBody();
    echo "GET ".HOME_URL." -> {$status}".PHP_EOL;
    echo substr($body, 0, 200).(strlen($body) > 200 ? '...' : '').PHP_EOL;
}catch(GuzzleException $e){
    fwrite(STDERR, "FAIL: could not reach ".HOME_URL." - {$e->getMessage()}".PHP_EOL);
    exit(1);
}

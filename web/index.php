<?php

require_once '../goutte.phar';

use Goutte\Client;

$client = new Client();

$tt = array();

// fetch
$crawler = $client->request('GET', 'http://www.imdb.com/chart/top');

$crawler->filter('table.chart tbody tr td.titleColumn')->each(function ($node) use ($crawler, &$tt) {

    $tmp = array();
    // print $node->text() . "<br>";
    $matches = null;
    if (preg_match('#\(([0-9]{4})\)$#', $node->text(), $matches)) {
        $tmp['year'] = $matches[1];
    }

    $node->filter('a')->each(function ($node) use ($crawler, &$tmp) {
        // create Link object and fetch uri which has tt number
        $linkCrawler = $crawler->selectLink($node->text());
        $link = $linkCrawler->link();
        $uri =  $link->getUri();

        $matches = null;
        if (preg_match('/(tt[0-9]+)/', $uri, $matches)) {
            // append
            $tmp['tt_id'] = $matches[1];
        }
    });

    $tt[] = $tmp;
});

// echo json
header('Content-Type: application/json');
echo json_encode($tt);
exit;

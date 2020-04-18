<?php
declare(strict_types=1);

use League\Csv\Writer;
use Symfony\Component\DomCrawler\Crawler;

require_once "common.php";

$rankingResponse = $httpClient->request("GET", "https://www.metaculus.com/rankings/");

$domCrawler->addContent($rankingResponse->getContent());

$rankingTableCrawler = $domCrawler
    ->filter(".rankings-table");

$rows = [];
foreach ($rankingTableCrawler->filter("tr") as $rowDom) {

    $rowCrawler = new Crawler($rowDom);
    if($rowCrawler->filter("a")->getIterator()->count() === 0) {
        continue;
    }

    $row = [];

    $row["name"] = $rowCrawler->filter("a")->text();
    $row["profile url"] = $rowCrawler->filter("a")->attr("href");
    $row["points"] = (int)$rowCrawler->filter("td")->getNode(3)->textContent;

    $profileHtml = $httpClient->request("GET", "https://www.metaculus.com" . $row["profile url"])->getContent();

    $profileCrawler = new Crawler($profileHtml);

    $matches = [];
    preg_match(
        "/(?<predictions>\d+)\s*predictions.+?(?<questions>\d+)\s*questions.+?\s*(?<resolved>\d+)\s*resolved/",
        $profileHtml,
        $matches
    );

    $row["all predictions"] = (int)$matches["predictions"];
    $row["predicted questions"] = (int)$matches["questions"];
    $row["resolved questions"] = (int)$matches["resolved"];

    $rows[] = $row;

}

$csvWriter = Writer::createFromString("");

$csvWriter->insertOne(array_keys($rows[0]));
$csvWriter->insertAll($rows);
file_put_contents(__DIR__ . "/ranking.csv", $csvWriter->getContent());

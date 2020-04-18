<?php
declare(strict_types=1);

use Monolog\Logger;
use PhpOffice\PhpSpreadsheet;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;

require __DIR__ . '/vendor/autoload.php';

$logger = new Logger('Logger');

$domCrawler = new Crawler();
$filesystem = new Filesystem();

$httpClient = HttpClient::create();

$htmlTableReader = new PhpSpreadsheet\Reader\Html();

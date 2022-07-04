<?php
require_once("Model/Autoloader.class.php");
Autoloader::register(); 

ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');
error_reporting(E_ALL);
ini_set("display_errors", 1);



$time_start = microtime(true);
const API_KEY = "RGAPI-d1058bde-5c5f-456b-b920-f8d40b0abf77";
$api = new API(API_KEY);


$summoner = $api->getSummonerByName("Zankanotachi");
var_dump($summoner->getStatsFromChampionByNumber(202,19));
echo $api->nb;
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);


<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//player account
class Summoner extends AutoCreator
{

    private const URL_MATCHES = "https://europe.api.riotgames.com";

    //Get id of history

    public function getMatchesFromDay($numberOfDay) {
        $matches_id = $this->getMatchesIdsFromDay($numberOfDay);
        return $this->createMatchesFromArray($this->getMatchesFromIds($matches_id));
    }



    public function getStatsFromChampionByDay($champion_id,$numberOfDay) {

        $stats = [];
        foreach($this->getMatchesFromDay($numberOfDay) as $match) {
            $player = $match->getPlayersSortedByPuuid()[$this->puuid];
            if($player->championId == $champion_id)
                $stats[] = $player->getKda();
        }
        return $stats;
    }

    public function getStatsFromChampionByNumberOfGames($champion_id,$numberOfGame) {
        $stats = [];

        foreach($this->getMatchesFromIds($this->getHistoryId($numberOfGame,0)) as $match) {
            $player = $match->getPlayersSortedByPuuid()[$this->puuid];
            if($player->championId == $champion_id)
                $stats[] = $player->getKda();
        }
        return $stats;

    }
    public function getHistory($number, $offset = 0) : Matches
    {
        $matches_ids = $this->getHistoryId($number, $offset);
        return $this->createMatchesFromArray($this->getMatchesFromIds($matches_ids));
    }
    private function getMatchesFromIdsRequest($match_id)
    {
        return self::URL_MATCHES . "/lol/match/v5/matches/" . $match_id;
    }
    private function getSecondsFromDay($numberOfDay) {
        $secondsInHour = 3600;
        $hourInDay = 24;
        return time()-($numberOfDay*$secondsInHour*$hourInDay);
    }
    private function getMatchesFromIds($matches_ids) : Matches
    {
        $requests = [];
        foreach ($matches_ids as $match_id) {
            $requests[] = $this->getMatchesFromIdsRequest($match_id);
        }
        return $this->createMatchesFromArray($this->api->requests($requests));


    }

    private function createMatchesFromArray($matches) {
        foreach ($matches as $match_result) {
            $matches_results[] = new Matche($match_result, $this->api);
        }
        return new Matches($matches_results);
    }
    private function getHistoryId($number, $offset)
    {
        return $this->api->request($this->getIdsRequest($number,$offset));
    }

    private function getMatchesIdsFromDay($numberOfDay) {
        return $this->api->request($this->getIdsRequest(null,null,$this->getSecondsFromDay($numberOfDay)));
    }
    private function getIdsRequest($number = null,$offset= null,$startTime= null,$endTime= null,$type= null,$queue= null) {
        $args = ["count" => $number,"start"=> $offset,"startTime"=>$startTime,"endTime"=>$endTime,"type"=>$type,"queue"=>$queue];
        $i = 0;
        $baseRequest = self::URL_MATCHES . "/lol/match/v5/matches/by-puuid/" . $this->puuid . "/ids";
        foreach($args as $key => $arg) {
            if($arg != null) {
                $baseRequest .= ($i++ == 0 ? "?" : "&") . $key."=".$arg;
            }
        }
        return $baseRequest;
    }
}
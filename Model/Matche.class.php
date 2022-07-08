<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//Match
class Matche extends AutoCreator
{

    public function getSummoners()
    {
        $summoners = [];
        foreach ($this->metadata->participants as $puiid)
            $summoners[] = $this->api->getSummonerByPuuid($puiid);
        return $summoners;
    }

    public function getStats($puuid = null) {

    }
    //Get participants sorted by their puuid
    public function getPlayersSortedByPuuid()
    {
        $participants = [];

        foreach ($this->info->participants as $participant) {
            $participants[$participant->puuid] = new Player($participant, $this->api);
        }
        return $participants;
    }

    public function getPlayersSortedByRole()
    {
        $participants = [];
        foreach ($this->info->participants as $participant) {
            $participants[$participant->individualPosition] = new Player($participant, $this->api);
        }
        return $participants;
    }

    public function getPlayerInfoFromPuuid($puuid)
    {
        return $this->getPlayersSortedByPuuid()[$puuid];
    }
}


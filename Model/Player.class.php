<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//Player in game
class Player extends Autocreator
{


    public function getKda()
    {
        return ["kills" => $this->kills, "deaths" => $this->deaths, "assists" => $this->assists];
    }

    public function getChampion()
    {
        return new Champion(["championId" => $this->championId, "championName" => $this->championName], $this->api);
    }
}
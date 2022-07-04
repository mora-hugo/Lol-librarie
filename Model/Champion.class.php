<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
class Champion extends Autocreator
{
    public static array $champions;
    private const DDragonUrl = "http://ddragon.leagueoflegends.com";

    private static function getGlobalChampionById($championId): Champion|bool
    {
        foreach (self::$champions as $champion) {
            if ($champion->key == $championId) {
                return new Champion($champion);
            }
        }
        return false;
    }
    public function getDetailsById($championId): Champion|bool
    {
        return $this->getGlobalChampionById($championId);
    }

}
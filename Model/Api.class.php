<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//base class to call first methods
class API
{
    public $nb;
    private readonly String $api_key;
    private const URL1 = "https://euw1.api.riotgames.com";
    public readonly String $version;
    public function __construct(String $api_key)
    {
        $this->api_key = $api_key;
        Champion::$champions = $this->request("http://ddragon.leagueoflegends.com/cdn/12.12.1/data/fr_FR/champion.json");
        $this->version = $this->request("https://ddragon.leagueoflegends.com/api/versions.json")[0];
    }



    /* Champion */

    public function getChampionByName($championName)
    {
        $this->api->request("http://ddragon.leagueoflegends.com/cdn/12.12.1/data/fr_FR/champion/$championName.json");
    }
    /* Summoners */
    public function getSummonerByName($name): Summoner
    {
        $summoner = $this->request(self::URL1 . "/lol/summoner/v4/summoners/by-name/" . $name);
        if ($summoner != false) {
            return new Summoner($summoner, $this);
        }
    }

    public function getSummonerByPuuid($puuid)
    {
        $summoner = $this->request(self::URL1 . "/lol/summoner/v4/summoners/by-puuid/" . $puuid);
        if ($summoner != false) {
            return new Summoner($summoner, $this);
        }
    }

    /* Utils */

    private function addKey($request): String
    {
        return $request .= (str_contains($request, "?") ? "&" : "?") . "api_key=" . $this->api_key;
    }
    public function request(String $request, $haveKey = true): array|bool
    {
        $this->nb++;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $haveKey ? $this->addKey($request) : "");
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    public function requests(array $url_array): array|bool
    {
        $this->nb+=count($url_array);
        $threads = 0;
        $thread_width = count($url_array);
        $master = curl_multi_init();
        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
        );

        $results = array();

        $count = 0;
        foreach ($url_array as $url) {
            $url = $this->addKey($url);
            $ch = curl_init();
            $curl_opts[CURLOPT_URL] = $url;

            curl_setopt_array($ch, $curl_opts);
            curl_multi_add_handle($master, $ch); //push URL for single rec send into curl stack
            $results[$count] = array("url" => $url, "handle" => $ch);
            $threads++;
            $count++;
            if ($threads >= $thread_width) { //start running when stack is full to width
                while ($threads >= $thread_width) {
                    usleep(100);
                    while (($execrun = curl_multi_exec($master, $running)) === -1) {
                    }
                    curl_multi_select($master);
                    // a request was just completed - find out which one and remove it from stack
                    while ($done = curl_multi_info_read($master)) {
                        foreach ($results as &$res) {
                            if ($res['handle'] == $done['handle']) {
                                $res['result'] = curl_multi_getcontent($done['handle']);
                            }
                        }
                        curl_multi_remove_handle($master, $done['handle']);
                        curl_close($done['handle']);
                        $threads--;
                    }
                }
            }
        }
        do { //finish sending remaining queue items when all have been added to curl
            usleep(100);
            while (($execrun = curl_multi_exec($master, $running)) === -1) {
            }
            curl_multi_select($master);
            while ($done = curl_multi_info_read($master)) {
                foreach ($results as &$res) {
                    if ($res['handle'] == $done['handle']) {
                        $res['result'] = curl_multi_getcontent($done['handle']);
                    }
                }
                curl_multi_remove_handle($master, $done['handle']);
                curl_close($done['handle']);
                $threads--;
            }
        } while ($running > 0);
        curl_multi_close($master);
        $final_results = [];

        foreach ($results as $resF) {
            $final_results[] = json_decode($resF["result"]);
        }
        return $final_results;
    }
}
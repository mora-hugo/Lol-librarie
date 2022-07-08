<?php



require_once("Model/Autoloader.class.php");
Autoloader::register();


//base class to call first methods
class API
{
    public int $nb;

    private float $timer;
    private readonly String $api_key;
    private const URL1 = "https://euw1.api.riotgames.com";
    public readonly String $version;
    public function __construct(String $api_key)
    {
        $this->nb = 0;
        $this->timer = microtime(true);
        $this->api_key = $api_key;
        Champion::$champions = $this->request("http://ddragon.leagueoflegends.com/cdn/12.12.1/data/fr_FR/champion.json");
        $this->version = $this->request("https://ddragon.leagueoflegends.com/api/versions.json")[0];
    }

    public function waitRequest()
    {
        if (microtime(true) - $this->timer > 10) {
            $this->timer = microtime(true);
            $this->nb = 0;
        }
        if ($this->nb < 18) {
            $this->nb++;
        } else {
            sleep((int)(10 - (microtime(true) - $this->timer) + 0.5));
            $this->waitRequest();
        }
    }

    public function waitMultiRequests($url_array)
    {
        $final_results = [];

        foreach ($url_array as $url) {
            if (microtime(true) - $this->timer > 10) {
                $this->timer = microtime(true);
                $this->nb = 0;
            }
            if ($this->nb < 18) {
                $this->nb++;
                $final_results[] = $url;
            } else {
                return $final_results;
            }
        }
        return $final_results;
    }
    public function getNbRequests()
    {
        return $this->nb;
    }
    /* Champion */

    public function getChampionByName($championName)
    {
        $this->api->request("http://ddragon.leagueoflegends.com/cdn/12.12.1/data/fr_FR/champion/$championName.json");
    }
    /* Summoners */
    public function getSummonerByName($name): ?Summoner
    {
        $summoner = $this->request(self::URL1 . "/lol/summoner/v4/summoners/by-name/" . $name);
        if ($summoner) {
            return new Summoner($summoner, $this);
        }
        return null;
    }

    public function getSummonerByPuuid($puuid): Summoner
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



        $this->waitRequest();
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
        $curl_opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
        );
        $mh = curl_multi_init();
        $urls = $this->waitMultiRequests($url_array);

        $ch = [];
        foreach ($urls as $key => $value) {

            $value = $this->addKey($value);
            $ch[$key] = curl_init($value);
            foreach ($curl_opts as $opt_key => $opt_value) {
                curl_setopt($ch[$key], $opt_key, $opt_value);
            }
            curl_multi_add_handle($mh, $ch[$key]);
        }
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
        $final_results = [];
        foreach (array_keys($ch) as $key) {
            $this->nb++;
            $final_results[] = json_decode(curl_multi_getcontent($ch[$key]));
        }
        if (count($url_array) != count($urls)) {
            $new_urls = array_slice($url_array, count($urls));
            if (count($new_urls) > 0) {

                sleep((int)(10 - (microtime(true) - $this->timer) + 0.5));


                foreach ($this->requests($new_urls) as $value) {
                    $final_results[] = $value;
                }
            }
        }

        return $final_results;
    }
}
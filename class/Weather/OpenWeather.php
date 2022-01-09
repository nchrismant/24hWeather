<?php
namespace Météo\Weather;

use Météo\Weather\Exception\CurlException;
use Météo\Weather\Exception\HTTPException;
use Météo\Weather\Exception\UnauthorizedHTTPException;

class OpenWeather {

    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Récupère les informations météorologiques du jour.
     *
     * @param  string $id Id de la ville
     * @param  string $units Unité de la température
     * @param  string $lang Langue
     * @return array
     */
    public function getToday(int $id, string $units="metric", string $lang="fr") : array
    {
        $data = $this->callAPI("weather?id={$id}&units={$units}&lang={$lang}");
        return [
            'name' => $data['name'],
            'country' => $data['sys']['country'],
            'temp' => (int)$data['main']['temp'],
            'description' => $data['weather'][0]['description'],
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon'],
            'icon' => $data['weather'][0]['icon'],
            'feels_like' => (int)$data['main']['feels_like'],
            'min' => (int)$data['main']['temp_min'],
            'max' => (int)$data['main']['temp_max'],
            'humidity' => $data['main']['humidity'],
            'speed' => $data['wind']['speed'],
            'visibility' => $data['visibility'],
            'sunrise' => new \DateTime('@' . $data['sys']['sunrise']),
            'sunset' => new \DateTime('@' . $data['sys']['sunset']),
            'date' => new \DateTime()
        ];
    }
    
    /**
     * Récupère l'ID de la ville en fonction de son nom.
     *
     * @param  string $city Nom de la ville (peut être spécifié le code de pays : Paris,FR)
     * @param  string $units Unité de la température
     * @param  string $lang Langue
     * @return string
     */
    public function getIDByName(string $city, string $units="metric", string $lang="fr") : string
    {
        $data = $this->callAPI("weather?q={$city}&units={$units}&lang={$lang}");
        return $data['id'];
    }
    
    /**
     * Récupère l'ID de la ville en fonction de ses coordonnées.
     *
     * @param  float $float Latitude de la ville
     * @param  float $float Longitude de la ville
     * @param  float $units Unité de la température
     * @param  string $lang Langue
     * @return string
     */
    public function getIDByCoordinate(float $lat, float $lon, string $units="metric", string $lang="fr") : string
    {
        $data = $this->callAPI("weather?lat={$lat}&lon={$lon}&units={$units}&lang={$lang}");
        return $data['id'];
    }
    
    /**
     * Récupère les prévisions météorologiques d'une ville.
     *
     * @param  string $id ID de la ville
     * @param  string $units Unité de la température
     * @param  string $lang Langue
     * @return array
     */
    public function getForecast(string $id, string $units="metric", string $lang="fr") : array
    {
        $data = $this->callAPI("forecast/daily?id={$id}&cnt=8&units={$units}&lang={$lang}");
        foreach($data['list'] as $day) {
            $results[] = [
                'temp' => (int)$day['temp']['day'],
                'min' => (int)$day['temp']['min'],
                'max' => (int)$day['temp']['max'],
                'description' => $day['weather'][0]['description'],
                'icon' => $day['weather'][0]['icon'],
                'date' => new \DateTime('@' . $day['dt'])
            ];
        }
        return $results;
    }
    
    /**
     * Récupère les informations météorologiques d'une ville heure par heure.
     *
     * @param  float $lat Latitude de la ville
     * @param  float $lon Longitude de la ville
     * @param  string $units Unité de la température
     * @param  string $lang Langue
     * @return array
     */
    public function getHourly(float $lat, float $lon, string $units="metric", string $lang="fr") : array
    {
        $data = $this->callAPI("onecall?lat={$lat}&lon={$lon}&exclude=current,minutely,daily,alerts&units={$units}&lang={$lang}");
        foreach($data['hourly'] as $key => $hour) {
            if($key < 25) {
                $results[] = [
                    'temp' => (int)$hour['temp'],
                    'description' => $hour['weather'][0]['description'],
                    'icon' => $hour['weather'][0]['icon'],
                    'date' => new \DateTime('@' . $hour['dt'])
                ];
            }
            
        }
        return $results;
    }
    
    /**
     * Récupère les informations géographiques de l'utilisateur en fonction de son adresse IP.
     *
     * @param  string $ip
     * @return array
     */
    public function getUserCoord(string $ip) : array
    {
        $curl = curl_init("https://api.ipdata.co/{$ip}?api-key=0a1f5dece9d008507a5f25c75188f757523d37cf29c15aee47b5c310");
        curl_setopt_array($curl, [
            CURLOPT_CAINFO 		   => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cacert.pem',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $data = curl_exec($curl);
        if($data === false) {
            throw new CurlException($curl);
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        if($code !== 200) {
            curl_close($curl);
            if($code === 401) {
                $data = json_decode($data, true);
                throw new UnauthorizedHTTPException($data['message'], 401);
            }
            throw new HTTPException($data, $code);
        }
        curl_close($curl);
        $data = json_decode($data, true);
        return [
            'lat' => $data['latitude'],
            'lon' => $data['longitude']
        ];
    }
    
    /**
     * Récupère l'url de drapeau d'un pays.
     *
     * @param  string $code Code du pays
     * @return string
     */
    public function getFlag(string $code) : string
    {
        $curl = curl_init("https://restcountries.com/v2/alpha/{$code}");
        curl_setopt_array($curl, [
            CURLOPT_CAINFO 		   => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cacert.pem',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $data = curl_exec($curl);
        if($data === false) {
            throw new CurlException($curl);
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        if($code !== 200) {
            curl_close($curl);
            if($code === 401) {
                $data = json_decode($data, true);
                throw new UnauthorizedHTTPException($data['message'], 401);
            }
            throw new HTTPException($data, $code);
        }
        curl_close($curl);
        $data = json_decode($data, true);
        return $data['flags']['svg'];
    }
    
    /**
     * Appelle l'API d'OpenWeather.
     *
     * @param  string $endpoint Point d'arret de l'url de l'API
     * @return array
     */
    private function callAPI(string $endpoint) : array
    {
        $curl = curl_init("https://api.openweathermap.org/data/2.5/{$endpoint}&APPID={$this->apiKey}");
        curl_setopt_array($curl, [
            CURLOPT_CAINFO 		   => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cacert.pem',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $data = curl_exec($curl);
	    if($data === false) {
            throw new CurlException($curl);
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        if($code !== 200) {
            curl_close($curl);
            if($code === 401) {
                $data = json_decode($data, true);
                throw new UnauthorizedHTTPException($data['message'], 401);
            }
            throw new HTTPException($data, $code);
        }
        curl_close($curl);
        return json_decode($data, true);
    }
}

?>
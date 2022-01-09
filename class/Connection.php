<?php
namespace Météo;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'WeatherConf.conf.php';

use \PDO;

class Connection {
    
    /**
     * Récupère la connexion à la base de données
     *
     * @return PDO
     */
    public static function getPDO() : PDO 
    {
        /*return new PDO('mysql:dbname=mysql_dwa;host=localhost', 'nathan', 'A123456*', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);*/
        
        return new PDO('mysql:dbname='.WeatherConf::$pdoDB.';host='.WeatherConf::$pdoHost.'', WeatherConf::$pdoUser, WeatherConf::$pdoPassword, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
?>
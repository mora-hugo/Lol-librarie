<?php
require_once("Model/Autoloader.class.php");
Autoloader::register(); 
class Autoloader{

    /**
     * Enregistre notre autoloader
     */
    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Inclue le fichier correspondant à notre classe
     * @param $class string Le nom de la classe à charger
     */
    static function autoload($class){
        if(file_exists('Model/' . $class . '.class.php')) {
            require 'Model/' . $class . '.class.php';

        }
        else if(file_exists('Model/' . $class . '.php')) {
            require 'Model/' . $class . '.php';
        }
    }

}
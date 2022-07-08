<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//class to auto create attribute of response
abstract class AutoCreator
{
    public static $nb;
    protected Api $api;
    public function __construct($arguments, $api)
    {
        self::$nb++;
        echo "sur : " . self::$nb;

        // if (isset($arguments->status)) {
        //     throw new Exception($arguments->status->message . " - status code : " . $arguments->status->status_code);
        // }
        $this->api = $api;
        foreach ($arguments as $fieldName => $fieldContent) {

            $this->{$fieldName} = $fieldContent;
        }
    }
}
<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
//class to auto create attribute of response
abstract class AutoCreator
{
    protected Api $api;
    public function __construct($arguments, $api)
    {
        if(isset($arguments->status)) {
            echo "NB : ".$api->nb;
            throw new Exception($arguments->status->message . " - status code : ".$arguments->status->status_code );
        }
        $this->api = $api;
        foreach ($arguments as $fieldName => $fieldContent) {

            $this->{$fieldName} = $fieldContent;
        }
    }
}
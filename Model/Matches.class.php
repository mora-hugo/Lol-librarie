<?php
require_once("Model/Autoloader.class.php");
Autoloader::register();
class Matches extends MyArrayObject {


    protected function isMyClass($object): bool
    {
        return $object instanceof Matche;
    }
}
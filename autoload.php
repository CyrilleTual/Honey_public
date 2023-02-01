<?php

class Autoload
{
    /**
     * spl_autoload_register se délenche si appel à une classe inconnue (new) et 
     * appelle la methode inclusionAuto en passant le nom de la classe comme argument
     */
    static function register()
    {
        spl_autoload_register([
            __CLASS__,
            'inclusionAuto'
        ]);
    }


    public static function inclusionAuto($className)
    {
        /**
         * transforme le namespace (de la classe $className)en chemin valide vers la classe
         */
        // require_once __DIR__ . "/" . str_replace("\\", "/", $className . ".php");
        //require_once __DIR__ . "/" . lcfirst(str_replace("\\", "/", $className . ".php"));
        require_once lcfirst(str_replace("\\", "/", $className . ".php"));
    }
}

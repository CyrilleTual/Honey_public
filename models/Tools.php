<?php

namespace Models;

class Tools
{
    /**
     * Methode de generation d'une chaine aléatoire ex-> pour les tokens
     */
    public function randomChain($lenth = 10)
    {
        return substr(str_shuffle(str_repeat($code = '0123456789ABCDEFGHJKLMNPQRSTVWXYZacefhjkmnrstvwxyz', ceil($lenth / strlen($code)))), 1, $lenth);
    }
}

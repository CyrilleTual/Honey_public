<?php

namespace Models;


class ErrorMessages
{
    public function getMessages() :  array
    {
        $messagesErrors = [
            /* 0 */
            "Une erreur est apparue lors de l'envoi du formulaire !",
            /* 1 */ "Ce message indique que quelque chose pose un p'tit problème",
            /* 2 */ "Veuillez renseigner votre date de naissance",
            /* 3 */ "erreur",
            /* 4 */ "erreur",
            /* 5 */ "erreur",
            /* 6 */ 'Veuillez renseigner un email valide SVP !',
            /* 7 */ 'erreur',
            /* 8 */ 'Erreur identification',
            /* 9 */
        ];
        return $messagesErrors;
    }








}


/**
 * 
 * Utilisation : Dans le controller :
 * 
 * $errors = []; // initialisation du tableau des erreurs 
 * 
 * $errorsArray = NEW \Models\ErrorMessages(); // 
 * $messagesErrors = $errorsArray->getMessages();
 * 
 * et quand une erreur se produit on alimente le tableau des erreurs :
 * 
 * $errors[] = $messagesErrors [x];  où X est le numero du message d'erreur
 * 
 */
